<?php

abstract class dmFileLog extends dmLog
{
  protected
  $dispatcher,
  $filesystem,
  $serviceContainer,
  $options,
  $nbFields;
  
  public function __construct(sfEventDispatcher $dispatcher, dmFileSystem $filesystem, sfServiceContainer $serviceContainer, array $options = array())
  {
    $this->dispatcher = $dispatcher;
    $this->filesystem = $filesystem;
    $this->serviceContainer = $serviceContainer;
    
    $this->initialize($options);
  }

  public function getDefaultOptions()
  {
    return array_merge(parent::getDefaultOptions(), array(
      'rotation'            => true,
      'max_size_kilobytes'  => 2,
      'buffer_size'         => 1024 * 16,
      'enabled'             => true
    ));
  }
  
  public function initialize(array $options)
  {
    parent::initialize($options);
    
    if ('/' !== $this->options['file']{0})
    {
      $this->options['file'] = dmProject::rootify($this->options['file']);
    }

    $this->nbFields = count($this->fields);
  }
  
  public function log(array $data)
  {
    if(!$this->getOption('enabled'))
    {
      return;
    }
    
    try
    {
      $this->checkFile();
      
      $entry = $this->serviceContainer->getService($this->options['entry_service_name']);
      
      $entry->configure($data);
      
      $data = "\n".$this->encode($entry->toArray());
      
      if($fp = fopen($this->options['file'], 'a'))
      {
        fwrite($fp, $data);
        fclose($fp);
      }
      else
      {
        throw new dmException(sprintf('Can not log in %s', $this->options['file']));
      }
      
      if ($this->options['rotation'] && !$_SERVER['REQUEST_TIME']%10)
      {
        $this->checkRotation();
      }
    }
    catch(Exception $e)
    {
      if (sfConfig::get('dm_debug'))
      {
        throw $e;
      }
    }
  }
  
  protected function checkRotation()
  {
    $maxSize = $this->options['max_size_kilobytes'] * 1024 * 1024;
    
    if (filesize($this->options['file']) > $maxSize)
    {
      $logs = file($this->options['file'], FILE_IGNORE_NEW_LINES);
      file_put_contents($this->options['file'], implode("\n", array_slice($logs, round(count($logs)/2))));
      unset($logs);
    }
  }
  
  public function getEntries($max = 100, array $options = array())
  {
    $this->checkFile();
    
    $options = array_merge(array(
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
      
      // first line is nearly always corrupted. remove it from encodedLine and decrement filePosition to catch it next time
      $filePosition += $strlenFunction($encodedLines[0]);
      unset($encodedLines[0]);
      
      foreach(array_reverse($encodedLines) as $encodedLine)
      {
        if(empty($encodedLine))
        {
          continue;
        }
        
        if(!($data = $this->restoreKeys($this->decode($encodedLine))))
        {
          continue;
        }
        
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
      
      unset($encodedLines, $data);
    }

    return $entries;
  }
  
  protected function fixLog()
  {
    $this->clear();
    
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
    return implode('|', str_replace(array('|', "\n"), ' ', $array));
  }
  
  protected function decode($string)
  {
    return explode('|', $string);
  }

  protected function restoreKeys(array $arrayEntry)
  {
    if($this->nbFields !== count($arrayEntry))
    {
      $values = array();
      foreach($arrayEntry as $index => $value)
      {
        $values[$this->fields[$index]] = $value;
      }
      return $values;
    }

    return array_combine($this->fields, $arrayEntry);
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