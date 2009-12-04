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
      new sfCommandOption('clear-db', null, sfCommandOption::PARAMETER_NONE, 'Drop database ( all data will be lost )'),
      new sfCommandOption('no-confirmation', null, sfCommandOption::PARAMETER_NONE, 'Whether to force dropping of the database')
    ));

    $this->namespace = 'dm';
    $this->name = 'setup';
    $this->briefDescription = 'Safely setup a project. Can be run several times without side effect.';

    $this->detailedDescription = <<<EOF
Will create symlinks in your web directory,
Build models, forms and filters,
Load data,
generate missing admin modules...
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $this->logSection('diem', 'Setup '.dmProject::getKey());
    
    if (!$this->isProjectLocked())
    {
      $this->runTask('cache:clear');
      
      if (!$options['clear-db'])
      {
        $ret = $this->runTask('dm:check-need-migration');
        
        if (dmCheckNeedMigrationTask::REQUIRE_MIGRATION_TRUE == $ret)
        {
          if (!$this->askConfirmation(array_merge(
            array('The project requires a doctrine migration'),
            array('', 'Are you sure you want to continue without runnig migration tasks ? (y/N)')
          ), 'QUESTION_LARGE', false)
          )
          {
            $this->logSection('diem', 'task aborted');
      
            return 1;
          }
        }
      }
      
      $this->runTask('dm:upgrade');
      $this->runTask('dm:clear-cache');
    }
    
    $this->runTask('doctrine:build', array(), array('model' => true));

    if ($options['clear-db'] || $this->isProjectLocked())
    {
      $this->reloadAutoload();
      
      $this->runTask('doctrine:drop-db', array(), array('no-confirmation' => dmArray::get($options, 'no-confirmation', false)));

      if ($ret = $this->runTask('doctrine:build-db'))
      {
        return $ret;
      }
      
      $this->runTask('doctrine:build-sql');
      
      $this->runTask('doctrine:insert-sql');
      
      // well, we don't need migration classes anymore...
      sfToolkit::clearDirectory(dmProject::rootify('lib/migration/doctrine'));
    }
    
    $this->reloadAutoload();
    
    $this->withDatabase();
    
    $this->runTask('dm:clear-cache');
    
    $this->getContext()->reloadModuleManager();
    
    $this->runTask('doctrine:build-forms', array(), array('generator-class' => 'dmDoctrineFormGenerator'));
    
    $this->runTask('doctrine:build-filters', array(), array('generator-class' => 'dmDoctrineFormFilterGenerator'));

    $this->runTask('dm:data');

    $this->runTask('dm:publish-assets');

    $this->runTask('dm:clear-cache');
    
    $this->reloadAutoload();
    
    $this->getContext()->reloadModuleManager();

    $this->runTask('dmAdmin:generate');
    
    $this->logSection('diem', 'generate front modules');
    $this->context->get('filesystem')->sf('dmFront:generate');

    $this->runTask('dm:permissions');
    
    $this->runTask('dm:clear-cache');
    
    $this->logBlock('Setup successful', 'INFO_LARGE');
    
    $this->unlockProject();
  }
  
  protected function migrate()
  {
    throw new dmException('Disabled');
    
    switch($migrateResponse = $this->runTask('dm:generate-migration'))
    {
      case dmGenerateMigrationTask::UP_TO_DATE:
        break;

      case dmGenerateMigrationTask::DIFF_GENERATED:
        $this->logBlock('New doctrine migration classes have been generated', 'INFO_LARGE');
        $this->logSection('diem', 'You should check them in /lib/migration/doctrine,');
        $this->logSection('diem', 'Then decide if you want to apply changes.');
        
        if ($this->askConfirmation('Apply migration changes ? (y/N)', 'QUESTION', false))
        {
          $this->runTask('dm:clear-cache'); // load the new migration classes
          
          $migrationSuccess = 0 === $this->runTask('doctrine:migrate');
          
          if (!$migrationSuccess)
          {
            $this->logBlock('Can not apply migration changes', 'ERROR');
          }
        }
        
        if(empty($migrationSuccess))
        {
          if (!$this->askConfirmation('Continue the setup ? (y/N)', 'QUESTION', false))
          {
            $this->logSection('diem', 'Setup aborted.');
            exit;
          }
        }
        break;

      default:
        throw new dmException('Unexpected case : '.$migrateResponse);
    }
  }
  
  protected function unlockProject()
  {
    if ($this->isProjectLocked())
    {
      $this->getFilesystem()->remove(dmOs::join(sfConfig::get('dm_data_dir'), 'lock'));
      
      $password = Doctrine_Core::getConnectionByTableName('DmPage')->getOption('password');
      
      $this->logBlock('Your project is now ready for web access. See you on admin_dev.php.', 'INFO_LARGE');
      $this->logBlock('Your login is admin and your password is '.(empty($password) ? '"admin"' : 'the database password'), 'INFO_LARGE');
    }
  }
  
  protected function isProjectLocked()
  {
    return file_exists(dmOs::join(sfConfig::get('dm_data_dir'), 'lock'));
  }
}
