<?php

abstract class dmFrontModuleGenerator
{

  protected
  $module,
  $dispatcher,
  $filesystem,
  $formatter;

  public function __construct(dmProjectModule $module, sfEventDispatcher $dispatcher, dmFilesystem $filesystem)
  {
    $this->module = $module;
    $this->dispatcher = $dispatcher;
    $this->filesystem = $filesystem;
  }
  
  public function setFormatter(sfFormatter $formatter)
  {
    $this->formatter = $formatter;
  }
  
  protected function logError($message)
  {
    if ($this->formatter)
    {
      $message = $this->formatter->format(get_class($this).' '.$message, 'ERROR');
    }
    
    $this->dispatcher->notify(new sfEvent($this, 'command.log', array($message)));
  }

  abstract public function execute();

}