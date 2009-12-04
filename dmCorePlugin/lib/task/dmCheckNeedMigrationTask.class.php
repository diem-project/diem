<?php

class dmCheckNeedMigrationTask extends sfDoctrineBaseTask
{
  const
  REQUIRE_MIGRATION_TRUE  = 900001,
  REQUIRE_MIGRATION_FALSE = 900002;

  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', true),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
    ));

    $this->namespace = 'dm';
    $this->name = 'check-need-migration';
    $this->briefDescription = 'Check if your project needs a doctrine migration';

    $this->detailedDescription = $this->briefDescription;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $databaseManager = new sfDatabaseManager($this->configuration);
    $config = $this->getCliConfig();

    $this->logSection('diem', $this->briefDescription);

    if (!is_dir($config['migrations_path']))
    {
      $this->getFilesystem()->mkdirs($config['migrations_path']);
    }
    
    try
    {
      $this->callDoctrineCli('dm-check-need-migration', array(
        'yaml_schema_path' => $this->prepareSchemaFile($config['yaml_schema_path']),
      ));
      
      $requireMigration = false;
    }
    catch(dmRequireMigrationException $e)
    {
      $requireMigration = true;
    }
    
    if ($requireMigration)
    {
      $this->logBlock('The project requires a doctrine migration', 'INFO_LARGE');
      $this->logSection('diem', 'You should run the following tasks:');
      $this->logSection('diem', 'php symfony doctrine:generate-migrations-diff');
      $this->logSection('diem', 'php symfony doctrine:migrate');
      
      return self::REQUIRE_MIGRATION_TRUE;
    }
    else
    {
      return self::REQUIRE_MIGRATION_FALSE;
    }
  }
  
  /**
   * Calls a Doctrine CLI command.
   *
   * @param string $task Name of the Doctrine task to call
   * @param array  $args Arguments for the task
   *
   * @see sfDoctrineCli
   */
  public function callDoctrineCli($task, $args = array())
  {
    $config = $this->getCliConfig();

    $arguments = array('./symfony', $task);

    foreach ($args as $key => $arg)
    {
      if (isset($config[$key]))
      {
        $config[$key] = $arg;
      }
      else
      {
        $arguments[] = $arg;
      }
    }

    $cli = new sfDoctrineCli($config);
    $cli->registerTaskClass('dmCheckNeedMigration');
    $cli->setSymfonyDispatcher($this->dispatcher);
    $cli->setSymfonyFormatter($this->formatter);
    return $cli->run($arguments);
  }
  
}