<?php

class dmUnitTestHelper
{
  protected
  $context,
  $moduleManager;

  public function boot($app = 'admin', $env = 'test', $debug = true)
  {
    $projectRootDir = getcwd();
    $testRootDir = realpath(dirname(__FILE__).'/../../fixtures');

    // configuration
    require_once $projectRootDir.'/config/ProjectConfiguration.class.php';
    $configuration = ProjectConfiguration::getApplicationConfiguration($app, $env, $debug, $testRootDir);
    
    // autoloader
    $autoload = sfSimpleAutoload::getInstance(sfConfig::get('sf_cache_dir').'/project_autoload.cache');
    $autoload->loadConfiguration(sfFinder::type('file')->name('autoload.yml')->in(array(
      sfConfig::get('sf_symfony_lib_dir').'/config/config',
      sfConfig::get('sf_config_dir'),
    )));
    $autoload->register();
    
    // lime
    include $configuration->getSymfonyLibDir().'/vendor/lime/lime.php';

    $this->context = dmContext::createInstance($configuration);

    $this->initialize();

    return $this;
  }
  
  // Helper for cross platform testcases that validate output
  public function fixLinebreaks($content)
  {
    return str_replace(array("\r\n", "\n", "\r"), "\n", $content);
  }

  public function clearDatabase(lime_test $t = null)
  {
    if ($t)
    {
      $t->diag('Clearing project database');
    }

    foreach($this->moduleManager->getModulesWithModel() as $module)
    {
      try
      {
        $module->getTable()->createQuery()->delete()->execute();
      }
      catch(Exception $e)
      {
        $t->diag(sprintf('Can not delete %s records : %s', $module, $e->getMessage()));
      }
    }
  }

  public function loremizeDatabase($nb = 10, lime_test $t = null)
  {
    if ($t)
    {
      $t->diag('Loremizing database with '.$nb.' records by table');
    }

    $loremizer = new dmDatabaseLoremizer($this->getDispatcher());
    $loremizer->loremize($nb);
  }

  public function syncPages(lime_test $t = null)
  {
    if ($t) $t->diag('Launching page sync to restore page structure... this may take some time');

    $timer = dmDebug::timer('sync pages '.dmString::random(4));

    try
    {
      $this->context->get('page_synchronizer')->execute();
    }
    catch(Exception $e)
    {
      print_r($e->getTraceAsString());
      throw $e;
    }

    if ($t) $t->ok(true, sprintf('Pages synchronized in %01.2f s | %d pages', $timer->getElapsedTime(), dmDb::table('DmPage')->count()));
  }

  public function updatePageTreeWatcher(lime_test $t = null)
  {
    if ($t) $t->diag('Launching update on pageTreeWatcher to restore page structure... this may take some time');

    $timer = dmDebug::timer('pageTreeWatcher update '.dmString::random(4));

    try
    {
      $this->context->get('page_tree_watcher')->update();
    }
    catch(Exception $e)
    {
      print_r($e->getTraceAsString());
      throw $e;
    }

    if ($t) $t->ok(true, sprintf('Pages synchronized in %01.2f s | %d pages', $timer->getElapsedTime(), dmDb::table('DmPage')->count()));
  }

  public function initialize()
  {
    $this->moduleManager = $this->context->getModuleManager();
    
    dmDb::table('DmPage')->checkBasicPages();
  }

  public function getModuleManager()
  {
    return $this->moduleManager;
  }


  public function getDispatcher()
  {
    return $this->context->getEventDispatcher();
  }

  public function getModule($moduleKey)
  {
    return $this->moduleManager->getModuleOrNull($moduleKey);
  }

  public function get($service)
  {
    return $this->context->get($service);
  }
  
  public function ksort(array $array)
  {
    ksort($array);
    
    foreach($array as $value)
    {
      if(is_array($value))
      {
        ksort($value);
      }
    }
    
    return $array;
  }
}