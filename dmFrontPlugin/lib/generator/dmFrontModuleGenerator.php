<?php

abstract class dmFrontModuleGenerator
{

  protected
  $module,
  $dispatcher,
  $filesystem,
  $moduleDir,
  $formatter;

  public function __construct(dmProjectModule $module, sfEventDispatcher $dispatcher, dmFilesystem $filesystem, $moduleDir)
  {
    $this->module = $module;
    $this->dispatcher = $dispatcher;
    $this->filesystem = $filesystem;
    $this->moduleDir = $moduleDir;
  }
  
  public function setFormatter(sfFormatter $formatter)
  {
    $this->formatter = $formatter;
  }

  protected function log($message)
  {
    if ($this->formatter)
    {
      $message = $this->formatter->format(get_class($this).' '.$message);
    }

    $this->dispatcher->notify(new sfEvent($this, 'command.log', array($message)));
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