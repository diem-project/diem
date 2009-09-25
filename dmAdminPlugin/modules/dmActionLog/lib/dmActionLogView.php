<?php

class dmActionLogView extends dmLogView
{
  protected
  $rows = array(
    'time'     => 'renderTime',
    'user'     => 'renderUser',
    'action'   => 'renderAction',
    'type'     => 'renderType',
    'subject'  => 'renderSubject'
  );
  
  protected function getEntries($max)
  {
    return $this->log->getEntriesForUser($this->user, $max);
  }
  
  /*
   * Row renderers
   */
  protected function renderUser(dmActionLogEntry $entry)
  {
    return sprintf('%s%s<br /><span class=light>%s</span>',
      ($username = $entry->get('username')) ? sprintf('<strong class="mr10">%s</strong>', $username) : '',
      $entry->get('ip'),
      $entry->get('session_id')
    );
  }
  
  protected function renderTime(dmActionLogEntry $entry)
  {
    return str_replace(' CEST', '', $this->dateFormat->format($entry->get('time')));
//    return date('Y/m/d H:m:s', $entry->get('time'));
  }
  
  protected function renderSubject(dmActionLogEntry $entry)
  {
    return $entry->get('subject');
  }
  
  protected function renderAction(dmActionLogEntry $entry)
  {
    return '<span class="block '.$this->getActionClass($entry->get('action')).'">'.$this->i18n->__($entry->get('action')).'</span>';
  }
  
  protected function renderType(dmActionLogEntry $entry)
  {
    return '<strong>'.$this->i18n->__($entry->get('type')).'</strong>';
  }
  
  protected function getActionClass($action)
  {
    switch($action)
    {
      case 'create': $class = 's24 s24_add'; break;
      case 'update': $class = 's24 s24_edit'; break;
      case 'delete': $class = 's24 s24_delete'; break;
      case 'error':  $class = 's24 s24_error'; break;
      default:       $class = '';
    }
    
    return $class;
  }
}