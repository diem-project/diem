<?php

class dmRecordSecurityManager
{

  protected $context;
  protected $container;

  public function __construct(dmContext $context, sfServiceContainer $container)
  {
    $this->context = $context;
    $this->container = $container;
  }

  public function manage($kind, dmDoctrineRecord $record)
  {
    if($record->getDmModule() && $security = $record->getDmModule()->getOption('security'))
    {
      foreach($security as $app=>$config)
      {
        foreach($config as $actionType=>$actionConfig)
        {
          if(!empty($actionConfig)){
            $method = sprintf('manage%sFor%s', dmString::classify($kind), dmString::classify($actionType));
            $this->$method($record, $actionConfig);
          }
        }
      }
    }
  }

  protected function manageInsertForActions($record, $actionsConfig)
  {
    foreach($actionsConfig as $actionName=>$actionConfig)
    {
      $do="n";
    }
  }

  protected function manageDeleteForActions($record, $actionsConfig)
  {

  }
}