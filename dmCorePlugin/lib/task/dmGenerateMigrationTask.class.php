<?php

class dmGenerateMigrationTask extends sfDoctrineBaseTask
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
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev')
    ));

    $this->namespace = 'dm';
    $this->name = 'generate-migration';
    $this->briefDescription = 'Automatically generate the migration classes';

    $this->detailedDescription = $this->briefDescription;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    if (!count(dmProject::getModels()))
    {
      $this->logSection('diem', 'There is no model in the /lib/model/doctrine/base dir');
      return self::UP_TO_DATE;
    }
    
    $this->removeDoubleMigrations();
    
    try
    {
      $this->runTask('doctrine:generate-migrations-diff');
      
      $this->removeDoubleMigrations();
    
      $this->reloadAutoload();
      
      return self::DIFF_GENERATED;
    }
    catch(Doctrine_Task_Exception $e)
    {
      $this->logSection('diem', 'The database is up to date');
      return self::UP_TO_DATE;
    }
  }
  
  protected function removeDoubleMigrations()
  {
    $files = sfFinder::type('file')->in(dmProject::rootify('lib/migration/doctrine'));
    
    sort($files);
    
    if(count($files) < 2)
    {
      return;
    }
    
    foreach($files as $index => $file)
    {
      $code = file_get_contents($file);
      
      $code = str_replace(array("\n", ' '), '', $code);

      $code = preg_replace('/^.+{(.+)}(^})*$/uU', '$1', $code);
      
      if ($index > 0)
      {
        if ($code === $previousCode)
        {
          $this->getFilesystem()->remove($file);
          $removedClasses = true;
        }
      }
      
      $previousCode = $code;
      $previousFile = $file;
    }
  }
  
}
