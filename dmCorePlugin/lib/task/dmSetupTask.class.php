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
    $this->withDatabase();
    
    $this->logSection('diem', 'Setup '.dmProject::getKey());

    $this->runTask('dm:clear-cache');
    
    $this->migrate();
    
    $this->runTask('doctrine:build-model');

    if ($options['clear-db'])
    {
      $this->runTask('doctrine:build', array('db' => true));
    }
    
    $this->runTask('dm:build-forms');
    
    $this->runTask('dm:build-filters');

    $this->runTask('dm:data');

    $this->runTask('dm:publish-assets');

    $this->runTask('dmAdmin:generate');
    
    $this->runTask('dm:clear-cache');
    
    $this->logBlock('Setup successfull', 'INFO');
    
    $this->unlockProject();
  }
  
  protected function migrate()
  {
    switch($this->runTask('dm:migrate'))
    {
      case dmMigrateTask::UP_TO_DATE:
        break;
      case dmMigrateTask::DIFF_GENERATED:
        $this->logBlock('New doctrine migration classes have been generated', 'INFO_LARGE');
        $this->logSection('diem', 'You should check them in /lib/migration/doctrine,');
        $this->logSection('diem', 'Then decide if you want to apply changes.');
        $confirm = $this->askConfirmation('Apply migration changes ? (y/N)', 'QUESTION', false);
        
        if (!$confirm)
        {
          if (!$this->askConfirmation('Continue the setup ? (y/N)', 'QUESTION', false))
          {
            $this->logSection('diem', 'Setup aborted.');
            exit;
          }
        }
        
        if (!$this->runTask('doctrine:migrate'))
        {
          $this->logSection('diem', 'Can not apply migration changes');
          exit;
        }
        break;
      default:
        throw new dmException('Unexpected case');
    }
  }
  
  protected function unlockProject()
  {
    if (file_exists(dmOs::join(sfConfig::get('dm_data_dir'), 'lock')))
    {
      $this->get('filesystem')->remove(dmOs::join(sfConfig::get('dm_data_dir'), 'lock'));
      
      $this->logBlock('Your project is now ready for web access. See you on admin_dev.php. Your login is admin and your password is the database password.', 'INFO_LARGE');
    }
  }
  
}