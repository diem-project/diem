<?php

/**
 * Install Diem
 */
class dmSetupTask extends dmContextTask
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
    $this->withDatabase();
    
//    $this->setOption('clear', true);

    $this->log('Setup '.dmProject::getKey());

    $this->get('cache_manager')->clearAll();

    $this->runTask('dm:publish-assets');
    
    $this->migrate();
    
    $this->buildModel();

    if ($options['clear-db'])
    {
      $this->clearDb();
    }

    $this->buildForms();

    if (true)
    {
      $this->buildFilters();
    }

    $this->runTask('dm:data');

    $this->executeTask('dmAdmin:generate');
    
    $this->get('cache_manager')->clearAll();
    
    if (file_exists(dmOs::join(sfConfig::get('dm_data_dir'), 'lock')))
    {
      $this->get('filesystem')->remove(dmOs::join(sfConfig::get('dm_data_dir'), 'lock'));
      
      $this->logBlock('Your project is now ready for web access. See you on admin_dev.php. Your login is admin and your password is the database password.', 'INFO_LARGE');
    }
  }
  
  protected function migrate()
  {
    if (count(sfFinder::type('file')->maxDepth(0)->in(dmProject::rootify('lib/model/doctrine'))))
    {
      try
      {
        $this->executeTask('sfDoctrineGenerateMigrationsDiff');
      }
      catch(Doctrine_Task_Exception $e)
      {
        $this->log('The database is up to date');
      }
    }
  }
  
  protected function clearDb()
  {
    $this->log("clear database");

    $task = new sfDoctrineDropDbTask($this->dispatcher, $this->formatter);
    $task->run(array(), array());

    $task = new sfDoctrineBuildDbTask($this->dispatcher, $this->formatter);
    $task->run(array(), array());

    $task = new sfDoctrineInsertSqlTask($this->dispatcher, $this->formatter);
    $task->run(array(), array());
  }

  protected function buildModel()
  {
    $this->log("build doctrine model");

//    sfToolkit::clearDirectory(dmOs::join(sfConfig::get("sf_lib_dir"), "model/doctrine/base"));

    return $this->executeTask('sfDoctrineBuildModel');
  }

  protected function buildForms()
  {
    $this->log("build doctrine forms");

//    sfToolkit::clearDirectory(dmOs::join(sfConfig::get("sf_lib_dir"), "form/doctrine/base"));

    return $this->executeTask('dmDoctrineBuildForms');
  }

  protected function buildFilters()
  {
    $this->log("build doctrine filters");

//    sfToolkit::clearDirectory(dmOs::join(sfConfig::get("sf_lib_dir"), "filter/doctrine/base"));

    return $this->executeTask('sfDoctrineBuildFilters');
  }

  
}