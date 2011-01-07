<?php

abstract class dmContextTask extends dmBaseTask
{
  protected
  $context;

  protected function withDatabase()
  {
    return $this->getContext()->getDatabaseManager();
  }
  
  protected function get($service)
  {
    return $this->getContext()->get($service);
  }

  /**
   * Get the current context
   * @return dmContext
   */
  public function getContext()
  {
    if (null === $this->context)
    {
      if (!dmContext::hasInstance())
      {
        $this->logSection('diem', sprintf('Loading %s...', get_class($this->configuration)));
        dm::createContext($this->configuration);
      }
      
      $this->context = dmContext::getInstance();

      $this->context->get('filesystem')->setFormatter($this->formatter);
    }
    
    return $this->context;
  }

  protected function mkdir($path)
  {
    if (!$this->get('filesystem')->mkdir($path))
    {
      $this->logBlock(sprintf('Can not mkdir %s', $path), 'ERROR');
    }
  }

  protected function copy($from, $to)
  {
    if (!file_exists($to))
    {
      if (!copy($from, $to))
      {
        $this->logBlock(sprintf('Can not copy %s to %s', $from, $to), 'ERROR');
      }
      else
      {
        chmod($to, 0777);
      }
    }
  }

  protected function exec($command)
  {
    if(!$this->context->get('filesystem')->exec($command, array($this, 'logOutput'), array($this, 'logErrors')))
    {
      throw new dmException(implode(', ', $this->context->get('filesystem')->getLastExec()));
    }
  }

  public function logOutput($output)
  {
    if (false !== $pos = strpos($output, "\n"))
    {
      $this->outputBuffer .= substr($output, 0, $pos);
      $this->log($this->outputBuffer);
      $this->outputBuffer = substr($output, $pos + 1);
    }
    else
    {
      $this->outputBuffer .= $output;
    }
  }

  public function logErrors($output)
  {
    if (false !== $pos = strpos($output, "\n"))
    {
      $this->errorBuffer .= substr($output, 0, $pos);
      $this->log($this->formatter->format($this->errorBuffer, 'ERROR'));
      $this->errorBuffer = substr($output, $pos + 1);
    }
    else
    {
      $this->errorBuffer .= $output;
    }
  }

  protected function clearBuffers()
  {
    if ($this->outputBuffer)
    {
      $this->log($this->outputBuffer);
      $this->outputBuffer = '';
    }

    if ($this->errorBuffer)
    {
      $this->log($this->formatter->format($this->errorBuffer, 'ERROR'));
      $this->errorBuffer = '';
    }
  }
  
}