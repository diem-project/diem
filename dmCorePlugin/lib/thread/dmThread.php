<?php

abstract class dmThread extends dmConfigurable
{
  protected
  $configuration,
  $options,
  $databaseManager,
  $moduleManager;
  
  public function __construct(dmProjectConfiguration $configuration, array $options = array())
  {
    $this->configuration = $configuration;
    
    $this->initialize($options);
  }
  
  protected function initialize(array $options = array())
  {
    $this->configure($options);
    
    sfConfig::set('sf_logging_enabled', false);

    $culture = $this->getOption('culture');

    sfConfig::set('sf_default_culture', $culture);
    dmDoctrineRecord::setDefaultCulture($culture);
    dmConfig::setCulture($culture);
  }
  
  public function execute()
  {
    $this->getDatabaseManager();
    
    $this->doExecute();
    
    $this->getDatabaseManager()->shutdown();
  }
  
  abstract public function doExecute();
  
  protected function getDatabaseManager()
  {
    if (null === $this->databaseManager)
    {
      $this->databaseManager = new sfDatabaseManager($this->configuration, array('auto_shutdown' => false));
    }
    
    return $this->databaseManager;
  }
  
  protected function getModuleManager()
  {
    if (null === $this->moduleManager)
    {
      $this->configuration->getConfigCache()->registerConfigHandler('config/dm/modules.yml', 'dmModuleManagerConfigHandler');
      
      $mm = include($this->configuration->getConfigCache()->checkConfig('config/dm/modules.yml'));
      
      dmModule::setManager($mm);
      dmDoctrineTable::setModuleManager($mm);
      dmDoctrineQuery::setModuleManager($mm);
    
      $this->moduleManager = $mm;
    }
    
    return $this->moduleManager;
  }
  
  protected function perf($kill = false)
  {
    if ($kill)
    {
      dmDebug::killForce(dmOs::getPerformanceInfos());
    }
    
    dmDebug::showForce(dmOs::getPerformanceInfos());
  }
}