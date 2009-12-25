<?php

class dmProjectLoremizer extends dmConfigurable
{
  protected
  $moduleManager,
  $serviceContainer;

  public function __construct(dmModuleManager $moduleManager, sfServiceContainer $serviceContainer, array $options = array())
  {
    $this->serviceContainer = $serviceContainer;
    $this->moduleManager    = $moduleManager;
    
    $this->initialize($options);
  }
  
  public function getDefaultOptions()
  {
    return array(
      'nb_records_per_table' => 10
    );
  }
  
  public function initialize(array $options)
  {
    $this->configure($options);
  }

  public function execute($nbRecordsPerTable = 10)
  {
    if(null !== $nbRecordsPerTable)
    {
      $this->setOption('nb_records_per_table', $nbRecordsPerTable);
    }
    
    $loremizer = $this->serviceContainer->getService('table_loremizer')
    ->setOption('nb_records', $this->getOption('nb_records_per_table'))
    ->setOption('create_associations', false);
    
    foreach($this->moduleManager->getProjectModules() as $module)
    {
      /*
       * Start with root modules with model
       */
      if (!$module->hasModel() || $module->hasParent() || $module->getModel() === 'DmMedia')
      {
        continue;
      }

      $this->executeModuleRecursive($module, $loremizer);
    }
    
    foreach($this->moduleManager->getProjectModules() as $module)
    {
      if ($module->hasModel())
      {
        $loremizer->executeAssociations($module->getTable());
      }
    }
  }
  
  public function executeModuleRecursive(dmModule $module, dmTableLoremizer $loremizer)
  {
    $loremizer->execute($module->getTable());

    foreach($module->getChildren() as $child)
    {
      $this->executeModuleRecursive($child, $loremizer);
    }
  }
  
}