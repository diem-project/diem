<?php

class dmPageTreeWatcher
{
  protected
  $dispatcher,
  $moduleManager,
  $serviceContainer,
  $modifiedTables;

  public function __construct(sfEventDispatcher $dispatcher, dmModuleManager $moduleManager, dmBaseServiceContainer $serviceContainer)
  {
    $this->dispatcher = $dispatcher;
    $this->moduleManager = $moduleManager;
    $this->serviceContainer = $serviceContainer;
    
    $this->initialize();
  }

  public function initialize()
  {
    $this->modifiedTables = array();
  }
  
  public function connect()
  {
    $this->dispatcher->connect('dm.controller.redirect', array($this, 'listenToControllerRedirectionEvent'));
    
    $this->dispatcher->connect('dm.record.modification', array($this, 'listenToRecordModificationEvent'));
  }
  
  public function listenToRecordModificationEvent(sfEvent $event)
  {
    $table = $event->getSubject()->getTable();
    
    if ($table instanceof dmDoctrineTable && !isset($this->modifiedTables[$table->getComponentName()]) && $table->interactsWithPageTree())
    {
      $this->addModifiedTable($table);
    }
  }
  
  public function addModifiedTable(dmDoctrineTable $table)
  {
    $model = $table->getComponentName();
    
    if (!isset($this->modifiedTables[$model]))
    {
      $this->modifiedTables[$model] = $table;
    }
  }

  public function listenToControllerRedirectionEvent(sfEvent $event)
  {
    $this->update();
  }

  public function update()
  {
    $modifiedModules = $this->getModifiedModules();
    
    if(!empty($modifiedModules))
    {
      $this->serviceContainer->getService('page_synchronizer')->execute($modifiedModules);
      
      $this->serviceContainer->getService('seo_synchronizer')->execute($modifiedModules);
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
          if ($localModule = $this->moduleManager->getModuleByModel($localRelation['class']))
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