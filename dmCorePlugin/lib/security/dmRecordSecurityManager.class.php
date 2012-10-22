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

  public function manage($hookType, dmDoctrineRecord $record)
  {
    if($record instanceof dmRecordPermission && 'DmRecordPermission' === $record->get('secure_model'))
    {
      $permPermRecord = $record->getTable()->find($record->get('secure_record'));
      if('DmRecordPermission' === $permPermRecord->get('secure_model')){
        return; //don't go over 3 levels of permission of permission, crazy boy !
      }
    }
    $security = $record->getDmModule()->getOption('security', false);
    if(is_array($security))
    {
      foreach($security as $app=>$appConfig)
      {
        if(!is_array($appConfig)) continue;
        foreach($appConfig as $actionKind=>$actionsConfig)
        {
          if(!is_array($actionsConfig)) continue;
          foreach($actionsConfig as $actionName=>$actionConfig)
          {
            if(!is_array($actionConfig)) continue;
            if(isset($actionConfig['strategy']) && in_array($actionConfig['strategy'], array('record', 'mixed')))
            {
              $method = 'manage' . ucfirst($hookType);
              if(method_exists($this, $method)){
                $this->$method($record, $actionName, $actionConfig, $app);
              }
            }
          }
        }
      }
    }
  }

  protected function manageInsert(dmDoctrineRecord $record, $actionName, $actionConfig, $app)
  {
    $permission = new DmRecordPermission();
    $permission->set('secure_module', $record->getDmModule()->getSfName());
    $permission->set('secure_action', $actionName);
    $permission->set('secure_model', get_class($record));
    $permission->set('secure_record', $record->get($record->getTable()->getIdentifier()));
    $permission->set('description', sprintf('Secure access to action %s of module %s for record "%s" of class %s', $actionName, $record->getDmModule()->getKey(), $record->__toString(), get_class($record)));
    
    $permission->save();
  }
  
  protected function manageDelete(dmDoctrineRecord $record, $actionName, $actionConfig, $app)
  {
    $query = Doctrine_Core::getTable('DmRecordPermission')->createQuery()
    ->select('id')
    ->andWhere('secure_model = ?', get_class($record))
    ->andWhere('secure_record = ?', $record->get($record->getTable()->getIdentifier()));
    
    $permissions = $query->execute();
    
    if($permissions->count() === 0) return;
    foreach($permissions as $permission)
    {
      $permission->delete();
    }
  }
  
  protected function manageUpdate(dmDoctrineRecord $record, $actionName, $actionConfig, $app)
  {
  	$do = "nothgin";
  }
}
