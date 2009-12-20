<?php

class dmUnitTestHelper
{
  protected
  $context,
  $moduleManager;

  public function boot($app = 'admin', $env = 'test', $debug = true)
  {
    $rootDir = getcwd();

    // configuration
    require_once $rootDir.'/config/ProjectConfiguration.class.php';
    $configuration = ProjectConfiguration::getApplicationConfiguration($app, $env, $debug, $rootDir);

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

    register_shutdown_function(array($this, 'cleanup'));
    
    $this->cleanup();

    return $this;
  }

  function cleanup()
  {
    // try/catch needed due to http://bugs.php.net/bug.php?id=33598
    try
    {
      sfToolkit::clearDirectory(sfConfig::get('sf_log_dir'));
      sfToolkit::clearDirectory(sfConfig::get('sf_upload_dir'));
      sfToolkit::clearDirectory(dmOs::join(sfConfig::get('sf_web_dir'), 'cache'));
      sfToolkit::clearDirectory(dmOs::join(sfConfig::get('sf_root_dir'), 'cache'));
      
      $this->get('filesystem')->remove(dmOs::join(sfConfig::get('sf_web_dir'), 'dm'));
      @unlink(dmOs::join(sfConfig::get('sf_web_dir'), 'sf'));
      
      $this->get('filesystem')->remove(sfFinder::type('any')->not_name('*.sqlite')->in(sfConfig::get('sf_data_dir')));

      copy(dmOs::join(sfConfig::get('sf_data_dir'), 'fresh_db.sqlite'), dmOs::join(sfConfig::get('sf_data_dir'), 'db.sqlite'));
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