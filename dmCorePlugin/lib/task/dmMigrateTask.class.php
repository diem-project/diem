<?php

class dmMigrateTask extends sfDoctrineBaseTask
{
  const
  UP_TO_DATE = 4001,
  DIFF_GENERATED = 4002;
  
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
    $this->briefDescription = 'Automatically migrate the database';

    $this->detailedDescription = $this->briefDescription;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $migration = new Doctrine_Migration(dmProject::rootify('lib/migration/doctrine'));
    $version = $migration->getCurrentVersion();
    
    $this->logSection('diem', 'Current database version : '.$version);
    
    if (!count(dmProject::getModels()))
    {
      $this->logSection('diem', 'There is no model in the /lib/model/doctrine dir');
      return;
    }
    
    try
    {
      $this->runTask('doctrine:generate-migrations-diff');
    }
    catch(Doctrine_Task_Exception $e)
    {
      $this->logSection('diem', 'The database is up to date');
      return self::UP_TO_DATE;
    }
    
    return self::DIFF_GENERATED;
  }
  
}