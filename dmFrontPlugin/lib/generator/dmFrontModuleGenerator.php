<?php

abstract class dmFrontModuleGenerator
{

  protected
  $module,
  $dispatcher,
  $filesystem;

  public function __construct(dmProjectModule $module, sfEventDispatcher $dispatcher, dmFilesystem $filesystem)
  {
    $this->module = $module;
    $this->dispatcher = $dispatcher;
    $this->filesystem = $filesystem;
  }

  abstract public function execute();

}