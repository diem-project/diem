<?php

require_once(realpath(dirname(__FILE__).'/../../bootstrap/unit.php'));

class dmTestHelper
{
	protected
	$context,
	$dispatcher,
	$moduleManager,
	$records = array();

	public function boot($app = 'admin', $env = 'test', $debug = true)
	{
		$this->dispatcher = new sfEventDispatcher();
		$appConfig = ProjectConfiguration::getApplicationConfiguration($app, $env, $debug, null, $this->dispatcher);
		$this->context = dmContext::createInstance($appConfig);
		
//    $this->context->getDoctrineConfig()->configureCache();

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

	protected function prepareRecords()
	{
		$this->loremizeDatabase(5);

		$this->records['example'] = dmDb::query('Example e')
    ->orderBy('RAND()')
    ->fetchRecord();

    $rubriqueIds = dmDb::query('Rubrique r, r.Infos i')
    ->select('r.id')
    ->where('i.id IS NOT NULL')
    ->fetchValues();

    $this->records['rubrique'] = dmDb::table('Rubrique')->find($rubriqueIds[rand(0, count($rubriqueIds)-1)]);

    $this->records['info'] = $this->records['rubrique']->getRelatedRecord('Info');

    $this->records['domaine'] = $this->records['rubrique']->Domaine;


    $docIds = dmDb::query('Doc d, d.Roles r')
    ->select('d.id')
    ->where('r.id IS NOT NULL')
    ->fetchValues();

    $this->records['doc'] = dmDb::table('Doc')->find($docIds[rand(0, count($docIds)-1)]);

    $this->records['role'] = $this->records['doc']->Roles[0];

    $this->records['docCateg'] = $this->records['doc']->DocCateg;

    $this->records['docType'] = $this->records['docCateg']->DocType;



    $this->records['feature'] = dmDb::query('Feature f')
    ->orderBy('RAND()')
    ->fetchRecord();

    $this->records['featureCateg'] = $this->records['feature']->FeatureCateg;

    $this->records['featureType'] = $this->records['featureCateg']->FeatureType;
	}

	public function get($module)
	{
    if (empty($this->records))
    {
    	$this->prepareRecords();
    }

    if (!isset($this->records[$module]))
    {
    	throw new dmException(sprintf('dmTestHelper has no %s record', $module));
    }

    return $this->records[$module];
	}

	public function getDispatcher()
	{
		return $this->dispatcher;
	}

	public function getObjects($moduleKey, myDoctrineRecord $record = null)
	{
		if (!$module = $this->getModule($moduleKey))
		{
			return null;
		}

		if ($table  = $module->getTable())
		{
			if (is_null($record))
			{
				$record = $table->create();
			}
		}

		if ($record && $record->exists() && $module->hasPage())
		{
			$page = $record->getDmPage();
		}
		else
		{
			$page = null;
		}

		return array(
      'module'    => $module,
      'model'     => $module->getModel(),
		  'table'     => $table,
		  'record'    => $record,
		  'page'      => $page
		);
	}

	public function getModule($moduleKey)
	{
		return $this->moduleManager->getModuleOrNull($moduleKey);
	}

}