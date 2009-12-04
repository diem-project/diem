<?php

class dmCheckNeedMigration extends Doctrine_Task
{
  public
  $description          =   'Generate migration classes from a generated difference between your models and yaml schema files',
  $requiredArguments    =   array('migrations_path'  => 'Specify the path to your migration classes folder.',
                                           'yaml_schema_path' => 'Specify the path to your yaml schema files folder.'),
  $optionalArguments    =   array('models_path'      => 'Specify the path to your doctrine models folder.');

  public function execute()
  {
    $migrationsPath = $this->getArgument('migrations_path');
    $modelsPath = $this->getArgument('models_path');
    $yamlSchemaPath = $this->getArgument('yaml_schema_path');

    $migration = new Doctrine_Migration($migrationsPath);
    $diff = new Doctrine_Migration_Diff($modelsPath, $yamlSchemaPath, $migration);
    $changes = $diff->generateChanges();
    
    if ($this->isEmpty($changes))
    {
      $this->notify('The project is up to date, and does NOT require a doctrine migration');
    }
    else
    {
      throw new dmRequireMigrationException('The project requires a doctrine migration');
    }
  }
  
  protected function isEmpty(array $changes)
  {
    foreach($changes as $key => $changes)
    {
      if (!empty($changes))
      {
        return false;
      }
    }
    
    return true;
  }
}

class dmRequireMigrationException extends dmException
{
  
}