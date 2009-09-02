<?php

class dmPageTreeWatcher
{
	protected
	$dmContext,
	$modifiedTables;

	public function __construct(dmContext $dmContext)
	{
		$this->dmContext = $dmContext;

		$this->dmContext->getSfContext()->getEventDispatcher()->connect('dm.controller.redirect', array($this, 'listenRedirection'));

		$this->initialize();
	}

	public function initialize()
	{
		$this->modifiedTables = array();
	}

	
	public function addModifiedTable(myDoctrineTable $table)
	{
		$model = $table->getComponentName();
		if (!isset($this->modifiedTables[$model]))
		{
      $this->modifiedTables[$model] = $table;
		}
	}

	public function listenRedirection(sfEvent $event)
	{
		$this->update();
	}

	public function update()
	{
		$modifiedModules = $this->getModifiedModules();
		
		if(!empty($modifiedModules))
		{
			if (sfConfig::get('sf_debug'))
			{
			  dm::getUser()->logAlert('dmPageTreeWatcher::update '.implode(', ', $modifiedModules));
			}

			$dispatcher = sfContext::getInstance()->getEventDispatcher();
			
      $service = new dmPageSyncService($dispatcher);
      $service->execute($modifiedModules);
      
      $service = new dmUpdateSeoService($dispatcher);
      $service->execute($modifiedModules);
		}

		$this->initialize();
	}

	public function getModifiedModules()
	{
		$modifiedModules = array();
		foreach($this->modifiedTables as $table)
		{
			/*
			 * If table belongs to a project module,
			 * it may interact with tree
			 */
			if ($module = $table->getDmModule())
			{
				if ($module->interactsWithPageTree())
				{
					$modifiedModules[$module->getKey()] = $module;
				}
			}
			/*
			 * If table owns project tables,
			 * it may interact with tree
			 */
			else
			{
				foreach($table->getRelationHolder()->getLocals() as $localRelation)
				{
					if ($localModule = dmModuleManager::getModuleByModel($localRelation['class']))
					{
						if ($localModule->interactsWithPageTree())
						{
							$modifiedModules[$localModule->getKey()] = $localModule;
						}
					}
				}
			}
		}

		return $modifiedModules;
	}
}