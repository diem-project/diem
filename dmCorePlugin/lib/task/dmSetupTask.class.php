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

    $this->createAssetSymlinks();
    
    $this->updateIncrementalSkeleton();

//    $this->updateMysql();

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

    $this->loadData();

    $this->generateAdmins();
    
    $this->generateFunctionalTests();

    $this->get('cache_manager')->clearAll();
  }
  
  protected function updateIncrementalSkeleton()
  {
    $incrementalSkeletonPath = dmOs::join(sfConfig::get('dm_core_dir'), 'data/incrementalSkeleton');

    foreach(sfFinder::type('dir')->maxDepth(0)->in($incrementalSkeletonPath) as $dir)
    {
      $userPath = sfConfig::get(basename($dir));
       
      foreach(sfFinder::type('dir')->in($dir) as $skelDir)
      {
        $userDir = dmOs::join($userPath, preg_replace('|^('.preg_quote($dir, '|').')|', '', $skelDir));
        $this->mkdir($userDir);
      }

      foreach(sfFinder::type('file')->in($dir) as $skelFile)
      {
        $userFile = dmOs::join($userPath, preg_replace('|^('.preg_quote($dir, '|').')|', '', $skelFile));
        $this->copy($skelFile, $userFile);
      }
    }
  }
  
  protected function generateFunctionalTests()
  {
    foreach(array('admin', 'front') as $app)
    {
      if (dmProject::appExists($app))
      {
        $file = dmProject::rootify('test/functional/'.$app.'/dmTest.php');
        
        if(!file_exists($file))
        {
          $this->get('filesystem')->mkdir(dirname($file));
          
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
    $this->executeTask('dmAdminGenerate');
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

  protected function loadData()
  {
    return $this->executeTask('dmData');
  }
  
  protected function createAssetSymlinks()
  {
    $projectWebPath = sfConfig::get('sf_web_dir');

    $this->get('filesystem')->mkdir($projectWebPath.'/dm');

    foreach(array("core", "admin", "front") as $plugin)
    {
      $pluginDir = dmOs::join(dm::getDir(), 'dm'.dmString::camelize($plugin).'Plugin');
      $origin = dmOs::join($pluginDir, "web");
      $target = dmOs::join($projectWebPath, sfConfig::get('dm_'.$plugin.'_asset', 'dm/'.$plugin));

      if (file_exists($origin))
      {
        $this->log("symlink $target");
        $this->get('filesystem')->relativeSymlink($origin, $target);
      }
    }

    $this->get('filesystem')->mkdir(sfConfig::get('sf_cache_dir').'/web');
    $this->get('filesystem')->relativeSymlink(
      sfConfig::get('sf_cache_dir').'/web',
      dmOs::join($projectWebPath, 'cache')
    );
  }
}