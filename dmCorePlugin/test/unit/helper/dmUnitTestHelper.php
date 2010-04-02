<?php

class dmUnitTestHelper
{
  protected
  $configuration,
  $context,
  $moduleManager;

  public function boot($app = 'admin', $env = 'test', $debug = true)
  {
    $this->bootFast($app, $env, $debug);

    // autoloader
    $autoload = sfSimpleAutoload::getInstance(sfConfig::get('sf_cache_dir').'/project_autoload.cache');
    $autoload->loadConfiguration(sfFinder::type('file')->name('autoload.yml')->in(array(
    sfConfig::get('sf_symfony_lib_dir').'/config/config',
    sfConfig::get('sf_config_dir'),
    )));
    $autoload->register();

    $this->initialize();

    dmDb::table('DmPage')->checkBasicPages();

    return $this;
  }

  public function bootFast($app = 'admin', $env = 'test', $debug = true)
  {
    $rootDir = getcwd();

    // configuration
    require_once $rootDir.'/config/ProjectConfiguration.class.php';
    $this->configuration = ProjectConfiguration::getApplicationConfiguration($app, $env, $debug, $rootDir);

    // lime
    include $this->configuration->getSymfonyLibDir().'/vendor/lime/lime.php';

    register_shutdown_function(array($this, 'cleanup'));

    $this->cleanup();

    new sfDatabaseManager($this->configuration);
  }

  protected function initialize()
  {
    $this->context = dmContext::createInstance($this->configuration);

    $this->moduleManager = $this->context->getModuleManager();
  }

  function cleanup()
  {
    // try/catch needed due to http://bugs.php.net/bug.php?id=33598
    try
    {
      if(method_exists($this->configuration, 'cleanup'))
      {
        $this->configuration->cleanup($this->context ? $this->context->get('filesystem') : new sfFilesystem());
      }
    }
    catch (Exception $e)
    {
      echo $e.PHP_EOL;
    }
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
      $this->clearModule($module);
    }
  }

  public function clearModule(dmModule $module)
  {
    try
    {
      $module->getTable()->createQuery()->delete()->execute();
    }
    catch(Exception $e)
    {
      $t->diag(sprintf('Can not delete %s records : %s', $module, $e->getMessage()));
    }

    foreach($module->getTable()->getRelationHolder()->getAssociations() as $alias => $association)
    {
      $association['refTable']->createQuery()->delete()->execute();
    }
  }

  public function loremizeDatabase($nb = 10, lime_test $t = null)
  {
    if ($t)
    {
      $t->comment('Loremizing database with '.$nb.' records by table');
    }

    $this->get('project_loremizer')->execute($nb);
  }
  
  public function loremizeModule($module, $nb = 10, lime_test $t = null)
  {
    if ($t)
    {
      $t->comment('Loremizing module '.$module.' with '.$nb.' records');
    }

    $this->get('table_loremizer')->execute($this->getModule($module)->getTable(), $nb);
  }

  public function syncPages(lime_test $t = null)
  {
    if ($t) $t->diag('Launching page sync to restore page structure... this may take some time');

    $timer = dmDebug::timer('sync pages '.dmString::random(4));

    $this->get('page_synchronizer')->execute();

    if ($t) $t->ok(true, sprintf('Pages synchronized in %01.2f s | %d pages', $timer->getElapsedTime(), dmDb::table('DmPage')->count()));
  }

  public function updatePageTreeWatcher(lime_test $t = null)
  {
    if ($t) $t->diag('Launching update on pageTreeWatcher to restore page structure... this may take some time');

    $timer = dmDebug::timer('pageTreeWatcher update '.dmString::random(4));

    $this->get('page_tree_watcher')->update();

    if ($t) $t->ok(true, sprintf('Pages synchronized in %01.2f s | %d pages', $timer->getElapsedTime(), dmDb::table('DmPage')->count()));
  }
  
  public function getModuleManager()
  {
    return $this->moduleManager;
  }

  public function getConfiguration()
  {
    return $this->configuration;
  }

  public function getDispatcher()
  {
    return $this->context->getEventDispatcher();
  }

  public function getServiceContainer()
  {
    return $this->context->getServiceContainer();
  }

  public function getContext()
  {
    return $this->context;
  }

  public function getModule($moduleKey)
  {
    return $this->moduleManager->getModuleOrNull($moduleKey);
  }

  public function get($service, $class = null)
  {
    return $this->context->get($service, $class);
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