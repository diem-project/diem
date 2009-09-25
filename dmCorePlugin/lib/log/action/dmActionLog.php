<?php

class dmActionLog extends dmFileLog
{
  protected
  $defaults = array(
    'file'                => 'data/dm/log/action.log',
    'entry_service_name'  => 'action_log_entry',
    'ignore_models'       => array(),
    'ignore_internal_actions' => true
  );
  
  public function connect()
  {
    $this->serviceContainer->getService('dispatcher')->connect('dm.record.modification', array($this, 'listenToRecordModificationEvent'));
    
    $this->serviceContainer->getService('dispatcher')->connect('application.throw_exception', array($this, 'listenToThrowException'));
  }
  
  public function getEntriesForUser(dmUser $user, $max = 0)
  {
    $entries = array();
    
    $encodedLines = array_reverse(file($this->options['file'], FILE_IGNORE_NEW_LINES));
    
    $count = 0;
    foreach($encodedLines as $encodedLine)
    {
      $data = $this->decode($encodedLine);
      
      if (!empty($data))
      {
        $entry = $this->serviceContainer->getService($this->options['entry_service_name']);
        $entry->setData($data);
        
        if (!$entry->isError() || $user->can('error_log'))
        {
          $entries[] = $entry;
          $count++;
          if ($max && $count == $max)
          {
            break;
          }
        }
      }
    }
    
    return $entries;
  }
  
  public function listenToThrowException(sfEvent $event)
  {
    $this->log(array(
      'server'  => $_SERVER,
      'user_id' => $this->serviceContainer->getService('user')->getGuardUserId(),
      'action'  => 'error',
      'type'    => 'exception',
      'subject' => $event->getSubject()->getMessage()
    ));
  }
  
  public function listenToRecordModificationEvent(sfEvent $event)
  {
    $record = $event->getSubject();
    
    if ($this->isIgnored($record))
    {
      return;
    }
    
    try
    {
      $subject = $record->__toString();
    }
    catch(Exception $e)
    {
      $subject = '-';
    }
  
    if ($record instanceof DmPage)
    {
      $type = $this->serviceContainer->getService('module_manager')->getModule('dmPage')->getKey();
    }
    elseif ($record instanceof dmDoctrineRecord && $module = $record->getDmModule())
    {
      $type = $module->getName();
    }
    else
    {
      $type = get_class($record);
    }
    
    $this->log(array(
      'server'  => $_SERVER,
      'user_id' => $this->serviceContainer->getService('user')->getGuardUserId(),
      'action'  => $event['type'],
      'type'    => $type,
      'subject' => $subject
    ));
  }
  
  protected function isIgnored($record)
  {
    if(in_array(get_class($record), $this->options['ignore_models']))
    {
      return true;
    }
    
    if (!$this->options['ignore_internal_actions'])
    {
      return false;
    }
    
    if ($record instanceof DmPage)
    {
      return true;
    }
    
    if ($module = $record->getDmModule())
    {
      return !$module->isProject();
    }
    
    return true;
  }
}