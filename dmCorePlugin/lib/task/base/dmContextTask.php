<?php

abstract class dmContextTask extends dmBaseTask
{
  protected
  $context;

  protected function withDatabase()
  {
    $this->getContext()->getDatabaseManager();
  }
  
  protected function get($service)
  {
    return $this->getContext()->get($service);
  }

  protected function getContext()
  {
    if (null === $this->context)
    {
      if (!dmContext::hasInstance())
      {
        dm::createContext($this->configuration);
      }
      
      $this->context = dmContext::getInstance();
    }
    
    return $this->context;
  }

  protected function mkdir($path)
  {
    if (!$this->get('filesystem')->mkdir($path))
    {
      $this->logBlock(sprintf('Can not mkdir %s', $path));
    }
    else
    {
      if (!@chmod($path, 0777))
      {
        //$this->alert('Can not chmod '.$path);
      }
    }
  }

  protected function copy($from, $to)
  {
    if (!file_exists($to))
    {
      if (!copy($from, $to))
      {
        $this->logBlock(sprintf('Can not copy %s to %s', $from, $to));
      }
      else
      {
        chmod($to, 0777);
      }
    }
  }
  
}