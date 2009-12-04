<?php

require_once(getcwd().'/test/bootstrap/unit.php');

class dmTestHelper
{
	protected
	$context,
	$moduleManager;

	public function boot($app = 'admin', $env = 'test', $debug = true)
	{
		$appConfig = ProjectConfiguration::getApplicationConfiguration($app, $env, $debug, null, $this->dispatcher);
		
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
      $module->getTable()->createQuery()->delete()->execute();
    }
	}

	public function loremizeDatabase($nb = 10, lime_test $t = null)
	{
		if ($t)
		{
			$t->diag('Loremizing database with '.$nb.' records by table');
		}
    
		$loremizer = new dmDatabaseLoremizer($this->dispatcher);
    $loremizer->loremize($nb);
	}

  public function syncPages(lime_test $t = null)
  {
    if ($t) $t->diag('Launching page sync to restore page structure... this may take some time');

    $timer = dmDebug::timer('sync pages '.dmString::random(4));

    $this->context->get('page_synchronizer')->execute();

    if ($t) $t->ok(true, sprintf('Pages synchronized in %01.2f s | %d pages', $timer->getElapsedTime(), dmDb::table('DmPage')->count()));
  }
  
  public function updatePageTreeWatcher(lime_test $t = null)
  {
    if ($t) $t->diag('Launching update on pageTreeWatcher to restore page structure... this may take some time');

    $timer = dmDebug::timer('pageTreeWatcher update '.dmString::random(4));

    $this->context->get('page_tree_watcher')->update();

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

}