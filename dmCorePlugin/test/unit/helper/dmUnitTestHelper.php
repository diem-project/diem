<?php

require_once(getcwd().'/test/bootstrap/unit.php');

class dmUnitTestHelper
{
	protected
	$context,
	$moduleManager;

	public function boot($app = 'admin', $env = 'test', $debug = true)
	{
		$appConfig = ProjectConfiguration::getApplicationConfiguration($app, $env, $debug, null, new sfEventDispatcher());
		
		$this->context = dmContext::createInstance($appConfig);

    $this->initialize();

    return $this;
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
}