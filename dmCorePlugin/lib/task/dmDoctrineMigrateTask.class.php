<?php

class dmDoctrineMigrateTask extends sfDoctrineGenerateMigrationsDiffTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    parent::configure();

    $this->namespace = 'dm';
    $this->name = 'migrate';
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $dir = sfConfig::get('sf_lib_dir').'/migration/doctrine';
    if (!is_dir($dir))
    {
      mkdir($dir, 0777, true);
    }
    
    require_once(dmOs::join(sfConfig::get('dm_core_dir'), 'lib/doctrine/task/Doctrine_Task_DmGenerateMigrationsDiff.php'));

    $databaseManager = new sfDatabaseManager($this->configuration);
    $this->callDoctrineCli('dm-generate-migrations-diff');
  }

}