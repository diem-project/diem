<?php

abstract class dmFileLog extends dmLog
{
  protected
  $dispatcher,
  $filesystem,
  $serviceContainer,
  $options;
  
  public function getDefaultOptions()
  {
    return array_merge(parent::getDefaultOptions(), array(
      'rotation'            => true,
      'max_size_megabytes'  => 3,
      'buffer_size'         => 1024 * 16
    ));
  }
  
  public function __construct(sfEventDispatcher $dispatcher, dmFileSystem $filesystem, sfServiceContainer $serviceContainer, array $options = array())
  {
    $this->dispatcher = $dispatcher;
    $this->filesystem = $filesystem;
    $this->serviceContainer = $serviceContainer;
    
    $this->initialize($options);
  }
  
  public function initialize(array $options)
  {
    parent::initialize($options);
    
    if ('/' !== $this->options['file']{0})
    {
      $this->options['file'] = dmProject::rootify($this->options['file']);
    }
  }
  
  public function log(array $data)
  {
    try
    {
      $this->checkFile();
      
      $entry = $this->serviceContainer->getService($this->options['entry_service_name']);
      
      $entry->configure($data);
      
      $data = $this->encode($entry->toArray());
  
      if (0 !== filesize($this->options['file']))
      {
        $data = "\n".$data;
      }
      
      if($fp = fopen($this->options['file'], 'a'))
      {
        fwrite($fp, $data);
        fclose($fp);
      }
      else
      {
        throw new dmException(sprintf('Can not log in %s', $this->options['file']));
      }
      
      if (dmArray::get($this->options, 'rotation', true))
      {
        $this->checkRotation();
      }
    }
    catch(Exception $e)
    {
      $this->dispatcher->notify(new sfEvent($this, 'application.log', array(
        'Can not log this request : '.$e->getMessage(),
        sfLogger::ERR
      )));
      
      if (sfConfig::get('dm_debug'))
      {
        throw $e;
      }
    }
  }
  
  protected function checkRotation()
  {
    if (rand(0, 20))
    {
      return;
    }

    $maxSize = dmArray::get($this->options, 'max_size_megabytes', 4) * 1024 * 1024;
    
    if (filesize($this->options['file']) > $maxSize)
    {
      $logs = file($this->options['file']);
      file_put_contents($this->options['file'], implode("\n", array_slice($logs, round(count($logs)/4))));
      unset($logs);
    }
  }
  
  public function getEntries($max = 100, array $options = array())
  {
    $this->checkFile();
    
    $options = array_merge(array(
      'fix_log' => true,
      'hydrate' => true,
      'keys'    => null,
      'filter'  => null
    ), $options);
    
    $file = $this->options['file'];
    $fileSize = filesize($file);
    $bufferSize = min($fileSize, $this->options['buffer_size']);
    
    $entries = array();
    $nb = 0;
    
    $filter = $options['filter'];
    
    $strlenFunction = function_exists('mb_strlen') ? 'mb_strlen' : 'strlen';
    
    for($filePosition = $fileSize - $bufferSize; $filePosition >= 0; $filePosition -= $bufferSize)
    {
      if (!$data = file_get_contents($file, 0, null, $filePosition, $bufferSize))
      {
        break;
      }
      
      $encodedLines = explode("\n", $data);
      
      // first line is corrupted. remove it from encodedLine and decrement filePosition to catch it next time
      if (!is_array($this->decode($encodedLines[0])))
      {
        $filePosition += $strlenFunction($encodedLines[0]);
        unset($encodedLines[0]);
      }
      
      foreach(array_reverse($encodedLines) as $encodedLine)
      {
        $data = $this->decode($encodedLine);
        
        if (!empty($data) && is_array($data))
        {
          if (!is_array($data)) dmDebug::kill($data);
          if ($filter && !call_user_func($filter, $data))
          {
            continue;
          }
          
          if ($options['hydrate'])
          {
            $entries[] = $this->buildEntry($data);
          }
          elseif($options['keys'])
          {
            $entry = array();
            foreach($options['keys'] as $key)
            {
              $entry[$key] = $data[$key];
            }
            $entries[] = $entry;
          }
          else
          {
            $entries[] = $data;
          }
          
          if ($max && (++$nb == $max))
          {
            break 2;
          }
        }
        elseif($options['fix_log'])
        {
          $this->fixLog();
          $options['fix_log'] = false;
          return $this->getEntries($max, $options);
        }
      }
      
      unset($encodedLines, $data);
    }

    return $entries;
  }
  
  protected function fixLog()
  {
    $lines = file($this->options['file'], FILE_IGNORE_NEW_LINES);
    // remove empty lines
    $lines = array_filter($lines);
    
    // separate collapsed lines
    $lines = str_replace('"}{"', "\"}\n{\"", $lines);
    
    file_put_contents($this->options['file'], implode("\n", $lines));
    
    unset($lines);
    
    $this->dispatcher->notify(new sfEvent($this, 'application.log', array(
      $this->getKey().' log has been fixed',
      sfLogger::NOTICE
    )));
  }
  
  protected function buildEntry(array $data)
  {
    $entry = $this->serviceContainer->getService($this->options['entry_service_name']);
    $entry->setData($data);
    return $entry;
  }
  
  protected function encode(array $array)
  {
    return json_encode($array);
  }
  
  protected function decode($string)
  {
    return json_decode($string, true);
  }
  
  protected function checkFile()
  {
    if (!$this->filesystem->mkdir(dirname($this->options['file'])))
    {
      throw new dmException(sprintf('Log dir %s can not be created', dirname($this->options['file'])));
    }
    
    if (!file_exists($this->options['file']))
    {
      if (!touch($this->options['file']))
      {
        throw new dmException(sprintf('Log file %s can not be created', $this->options['file']));
      }
      
      chmod($this->options['file'], 0777);
    }
  }
  
  public function clear()
  {
    $this->checkFile();
    file_put_contents($this->options['file'], '');
  }
  
  public function getSize()
  {
    $this->checkFile();
    return filesize($this->options['file']);
  }
}