<?php

class dmLoremizeService extends dmService
{
  const DEFAULT_NB = 10;

  public function execute()
  {
    $this->log("dmLoremize::execute");

    $timer = dmDebug::timerOrNull('dmLoremize::execute');

    if ($moduleName = $this->getOption('module_name'))
    {
      $loremizer = new dmModuleLoremizer($this->dispatcher);
      $loremizer->loremize(dmModuleManager::getModule($moduleName), $this->getOption('nb', self::DEFAULT_NB));
    }
    else
    {
      $loremizer = new dmDatabaseLoremizer($this->dispatcher);
      $loremizer->loremize($this->getOption('nb', self::DEFAULT_NB));
    }

    $timer && $timer->addTime();
  }

}