<?php

class dmSetupService extends dmService
{

  /*
   * Performs, verify, update Diem installation
   */
  public function execute()
  {
    $this->formatter->setMaxLineSize(1000);
    
    $this->setOption('clear', true);

    $this->log("Installation...");

    $this->clearCache();

    $this->createAssetSymlinks();

//    $this->updateMysql();

    $this->buildModel();

    if ($this->getOption('clear-db'))
    {
      $this->clearDb();
    }

    $this->buildForms();

    if (true)
    {
      $this->buildFilters();
    }

    $this->loadData();

//    $this->buildSprites();

    $this->generateAdmins();
    
    $this->generateFunctionalTests();

    $this->clearCache();
  }
  
//  protected function validateModel()
//  {
//    foreach(dmProject::getModels() as $model)
//    {
//      $table = dmDb::table($model);
//      foreach($table->getRelationHolder()->getAssociations() as $alias => $relation)
//      {
//        throw new dmException(sprintf('%s %s', $model, $alias));
//      }
//    }
//  }
  
  protected function generateFunctionalTests()
  {
    foreach(array('admin', 'front') as $app)
    {
      if (dmProject::appExists($app))
      {
        $file = dmProject::rootify('test/functional/'.$app.'/dmTest.php');
        
        if(!file_exists($file))
        {
          $this->filesystem->mkdir(dirname($file));
          
          file_put_contents($file, '<?php

require_once realpath(dirname(__FILE__).\'/../../../config/ProjectConfiguration.class.php\');

$config = array(
  \'env\'       => \'test\',
  \'debug\'     => false,
  \'login\'     => '.($app == 'admin' ? 'true' : 'false').',
  \'username\'  => \'admin\',
  \'password\'  => \'admin\'
);

$test = new dm'.ucfirst($app).'FunctionalCoverageTest($config);

$test->run();');
        }
      }
    }
  }

  protected function generateAdmins()
  {
    $this->executeService('dmAdminGenerate');
  }

  protected function clearCache()
  {
    return $this->executeService('dmClearCache');
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

    if($this->getOption("clear"))
    {
      sfToolkit::clearDirectory(dmOs::join(sfConfig::get("sf_lib_dir"), "model/doctrine/base"));
    }

    $task = new dmDoctrineBuildModelTask($this->dispatcher, $this->formatter);
    $task->run(array(), array());
  }

  protected function buildForms()
  {
    $this->log("build doctrine forms");

    if($this->getOption("clear"))
    {
      sfToolkit::clearDirectory(dmOs::join(sfConfig::get("sf_lib_dir"), "form/doctrine/base"));
    }

    $task = new dmDoctrineBuildFormsTask($this->dispatcher, $this->formatter);
    $task->run(array(), array());
  }

  protected function buildFilters()
  {
    $this->log("build doctrine filters");

    if($this->getOption("clear"))
    {
      sfToolkit::clearDirectory(dmOs::join(sfConfig::get("sf_lib_dir"), "filter/doctrine/base"));
    }

    $task = new dmDoctrineBuildFiltersTask($this->dispatcher, $this->formatter);
    $task->run(array(), array());
  }

  protected function loadData()
  {
    return $this->executeService('dmData');
  }
  protected function buildSprites()
  {
    return $this->executeService('dmSprite');
  }

  protected function createAssetSymlinks()
  {
    $projectWebPath = sfConfig::get("sf_web_dir");

    $this->filesystem->mkdir($projectWebPath.'/dm');

    foreach(array("core", "admin", "front") as $plugin)
    {
      $pluginDir = dmOs::join(dm::getDir(), 'dm'.dmString::camelize($plugin).'Plugin');
      $origin = dmOs::join($pluginDir, "web");
      $target = dmOs::join($projectWebPath, sfConfig::get("dm_{$plugin}_asset", "dm/$plugin"));

      if (file_exists($origin))
      {
        $this->log("symlink $target");
        $this->filesystem->relativeSymlink($origin, $target);
      }
    }

    $this->filesystem->mkdir(sfConfig::get('sf_cache_dir').'/web');
    $this->filesystem->relativeSymlink(
      sfConfig::get('sf_cache_dir').'/web',
      dmOs::join($projectWebPath, 'cache')
    );
  }

}