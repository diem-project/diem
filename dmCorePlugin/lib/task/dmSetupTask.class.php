<?php

/**
 * Install Diem
 */
class dmSetupTask extends dmServiceTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    parent::configure();

    $this->addOptions(array(
      new sfCommandOption('clear-db', null, sfCommandOption::PARAMETER_NONE, 'Drop database ( all data will be lost )')
    ));

    $this->namespace = 'dm';
    $this->name = 'setup';
    $this->briefDescription = 'Safely setup a project';

    $this->detailedDescription = <<<EOF
Will create symlinks in your web directory,
Build models, forms and filters,
Load data,
Rebuild sprites,
generate admin modules
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
//    if (!sfContext::hasInstance())
//    {
//      sfContext::createInstance($this->configuration);
//    }
//
//    new sfDatabaseManager($this->configuration);

    return $this->executeService("dmSetup", $options);
  }
}
