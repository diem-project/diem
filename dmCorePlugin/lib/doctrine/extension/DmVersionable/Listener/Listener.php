<?php

class dmDoctrineAuditLogListener extends Doctrine_AuditLog_Listener
{
  /**
   * Post insert event hook which creates the new version record
   * This will only insert a version record if the auditLog is enabled
   *
   * @param   Doctrine_Event $event
   * @return  void
   */
  public function postInsert(Doctrine_Event $event)
  {
    if ($this->_auditLog->getOption('auditLog'))
    {
      $class = $this->_auditLog->getOption('className');

      $record  = $event->getInvoker();
      $version = new $class();
      $version->merge($record->toArray(), false);
      
      try
      {
        $version->save();
      }
      catch(Doctrine_Connection_Exception $e)
      {
        // When using both Sortable and Versionable behaviors,
        // This fails and should be skipped.
      }
    }
  }
  
    /**
     * Pre update event hook for inserting new version record
     * This will only insert a version record if the auditLog is enabled
     *
     * @param  Doctrine_Event $event
     * @return void
     */
    public function preUpdate(Doctrine_Event $event)
    {
      $disabled = $event->getInvoker()->hasMappedValue('disable_versioning') ? $event->getInvoker()->get('disable_versioning') : false;
      
      if (!$disabled)
      {
        parent::preUpdate($event);
      }
    }
}