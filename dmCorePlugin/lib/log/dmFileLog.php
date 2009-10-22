<?php

abstract class dmFileLog extends dmLog
{
  protected
  $dispatcher,
  $filesystem,
  $serviceContainer,
  $options;
  
  protected function getDefaultOptions()
  {
    return array_merge(parent::getDefaultOptions(), array(
      'rotation'            => true,
      'max_size_megabytes'  => 4
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
    $this->checkFile();
    
    $entry = $this->serviceContainer->getService($this->options['entry_service_name']);
    
    $entry->configure($data);
    
    $data = $this->encode($entry->toArray());

    if($fp = fopen($this->options['file'], 'a'))
    {
      fwrite($fp, $data."\n");
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
      file_put_contents($this->options['file'], implode("\n", array_slice($logs, round(count($logs)/2))));
      unset($logs);
    }
  }
  
  public function getEntries($max = 100)
  {
    $entries = array();
    
    $encodedLines = array_reverse(file($this->options['file'], FILE_IGNORE_NEW_LINES));
    
    if($max)
    {
      $encodedLines = array_slice($encodedLines, 0, $max);
    }
    
    foreach($encodedLines as $encodedLine)
    {
      $data = $this->decode($encodedLine);
      
      if (!empty($data))
      {
        $entries[] = $this->buildEntry($data);
      }
    }
    
    unset($encodedLines);
    
    return $entries;
  }
  
  public function getFilteredEntries($max = 100, $filterCallback)
  {
    $entries = array();
    
    $encodedLines = array_reverse(file($this->options['file'], FILE_IGNORE_NEW_LINES));
    
    $nb = 0;
    
    foreach($encodedLines as $encodedLine)
    {
      $data = $this->decode($encodedLine);
      
      if (!empty($data) && call_user_func($filterCallback, $data))
      {
        $entries[] = $this->buildEntry($data);
        
        if ($max && (++$nb == $max))
        {
          break;
        }
      }
    }
    
    unset($encodedLines);
    
    return $entries;
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