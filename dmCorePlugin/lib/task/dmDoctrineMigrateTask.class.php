<?php

class dmDoctrineMigrateTask extends dmServiceTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    parent::configure();

    $this->addOptions(array(
    ));

    $this->namespace = 'dm';
    $this->name = 'migrate';
    $this->briefDescription = 'Migrate doctrine database';

    $this->detailedDescription = 'Migrate doctrine database';
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    return $this->executeService("dmDoctrineMigrate", $options);
  }

}