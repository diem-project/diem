<?php

class dmPageTreeWatcher extends dmConfigurable
{
  protected
  $dispatcher,
  $serviceContainer,
  $options,
  $modifiedTables;

  public function __construct(sfEventDispatcher $dispatcher, dmBaseServiceContainer $serviceContainer, array $options = array())
  {
    $this->dispatcher = $dispatcher;
    $this->serviceContainer = $serviceContainer;
    
    $this->initialize($options);
  }
  
  public function getDefaultOptions()
  {
    return array(
      'use_thread' => 'auto'
    );
  }

  public function initialize(array $options = array())
  {
    $this->configure($options);
    
    $this->reset();
  }
  
  public function reset()
  {
    $this->modifiedTables = array();
  }
  
  public function connect()
  {
    $this->dispatcher->connect('dm.controller.redirect', array($this, 'listenToControllerRedirectionEvent'));
    
    $this->dispatcher->connect('dm.record.modification', array($this, 'listenToRecordModificationEvent'));

    $this->dispatcher->connect('dm.record.page_missing', array($this, 'listenToRecordPageMissingEvent'));
  }

  public function listenToRecordPageMissingEvent(sfEvent $event)
  {
    $this->addModifiedRecord($event->getSubject())->update();

    $event->setReturnValue(dmDb::table('DmPage')->findOneByRecordWithI18n($event->getSubject()));

    return true;
  }
  
  public function listenToRecordModificationEvent(sfEvent $event)
  {
    $this->addModifiedRecord($event->getSubject());
  }

  public function addModifiedRecord(dmDoctrineRecord $record)
  {
    if ($record instanceof DmAutoSeo)
    {
      $table = $record->getTargetDmModule()->getTable();
    }
    else
    {
      $table = $record->getTable();
    }

    if ($table instanceof dmDoctrineTable)
    {
      if (!isset($this->modifiedTables[$table->getComponentName()]) && $table->interactsWithPageTree())
      {
        $this->addModifiedTable($table);
      }
    }

    return $this;
  }
  
  public function addModifiedTable(dmDoctrineTable $table)
  {
    $model = $table->getComponentName();
    
    if (!isset($this->modifiedTables[$model]))
    {
      $this->modifiedTables[$model] = $table;
    }

    return $this;
  }

  public function listenToControllerRedirectionEvent(sfEvent $event)
  {
    try
    {
      $this->update();
    }
    catch(Exception $e)
    {
      if (sfConfig::get('sf_debug'))
      {
        throw $e;
      }
    }
  }

  public function update()
  {
    $modifiedModules = $this->getModifiedModules();
    
    if(!empty($modifiedModules))
    {
      $this->synchronizePages($modifiedModules);

      $this->synchronizeSeo($modifiedModules);
    }

    $this->reset();
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
          $modifiedModules[] = $module->getKey();
        }
      }
      /*
       * If table owns project tables,
       * it may interact with tree
       */
      else
      {
        $moduleManager = $this->serviceContainer->getService('module_manager');
        
        foreach($table->getRelationHolder()->getLocals() as $localRelation)
        {
          if ($localModule = $moduleManager->getModuleByModel($localRelation->getClass()))
          {
            if ($localModule->interactsWithPageTree())
            {
              $modifiedModules[] = $localModule->getKey();
            }
          }
        }
      }
    }

    return $modifiedModules;
  }
  
  protected function useThread()
  {
    if ('auto' == $this->getOption('use_thread'))
    {
      $useThread = false;
      
      if (dmConfig::canSystemCall())
      {
        $apacheMemoryLimit = dmString::convertBytes(ini_get('memory_limit'));
        if($apacheMemoryLimit < 64 * 1024 * 1024)
        {
          $filesystem = $this->serviceContainer->getService('filesystem');
          
          if ($filesystem->exec('php -r "die(ini_get(\'memory_limit\'));"'))
          {
            $cliMemoryLimit = dmString::convertBytes($filesystem->getLastExec('output'));
            
            $useThread = ($cliMemoryLimit >= $apacheMemoryLimit);
          }
        }
      }

      $this->setOption('use_thread', $useThread);
    }
    
    return $this->getOption('use_thread');
  }
  
  public function synchronizePages(array $modules = array())
  {
    dmDb::table('DmPage')->checkBasicPages();
    
    if ($this->useThread())
    {
      $threadLauncher = $this->serviceContainer->getService('thread_launcher');
    
      $pageSynchronizerSuccess = $threadLauncher->execute('dmPageSynchronizerThread', array(
        'class'   => $this->serviceContainer->getParameter('page_synchronizer.class'),
        'modules' => $modules
      ));
    }
    else
    {
      $this->serviceContainer->getService('page_synchronizer')->execute($modules);
    }
  }
  
  public function synchronizeSeo(array $modules = array(), array $cultures = null)
  {
    $cultures = null === $cultures ? $this->serviceContainer->get('i18n')->getCultures() : $cultures;

    if ($this->useThread())
    {
      $this->serviceContainer->getService('thread_launcher')->execute('dmSeoSynchronizerThread', array(
        'class'     => $this->serviceContainer->getParameter('seo_synchronizer.class'),
        'cultures'  => $cultures,
        'modules'   => $modules
      ));
    }
    else
    {
      foreach($cultures as $culture)
      {
        $this->serviceContainer->getService('seo_synchronizer')->execute($modules, $culture);
      }
    }
  }
}