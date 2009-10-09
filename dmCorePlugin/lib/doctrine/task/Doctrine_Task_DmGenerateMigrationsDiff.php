<?php

class Doctrine_Task_DmGenerateMigrationsDiff extends Doctrine_Task_GenerateMigrationsDiff
{
    public function execute()
    {   
        $migrationsPath = $this->getArgument('migrations_path');
        $modelsPath = $this->getArgument('models_path');
        
        $yamlSchemaPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'dm_tmp_schema';
        @mkdir($yamlSchemaPath, 0777);
        sfToolkit::clearDirectory($yamlSchemaPath);
        $schemas = sfFinder::type('file')->name('*.yml')->in(array(
          $this->getArgument('yaml_schema_path'),
          dmOs::join(sfConfig::get('dm_core_dir'), 'config/doctrine'),
          dmOs::join(dm::getDir(), 'dmGuardPlugin/config/doctrine')
        ));
        foreach($schemas as $schema)
        {
          copy($schema, dmOs::join($yamlSchemaPath, dmString::random(8).'_'.basename($schema)));
        }
        
        $migration = new Doctrine_Migration($migrationsPath);
        
        require_once(dmOs::join(sfConfig::get('dm_core_dir'), 'lib/doctrine/migration/dmDoctrineMigrationDiff.php'));
        $diff = new dmDoctrineMigrationDiff($modelsPath, $yamlSchemaPath, $migration);
        
        $changes = $diff->generateMigrationClasses();

        $numChanges = count($changes, true) - count($changes);

        if ( ! $numChanges) {
            throw new Doctrine_Task_Exception('Could not generate migration classes from difference');
        } else {
            $this->notify('Generated migration classes successfully from difference');
        }
    }
}