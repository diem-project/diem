<?php

class dmActionLogView extends dmLogView
{
  protected
  $rows = array(
    'time'     => 'renderTime',
    'user'     => 'renderUser',
    'action'   => 'renderAction',
    'subject'  => 'renderSubject'
  );
  
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
    switch($action = $entry->get('action'))
    {
      case 'create': $class = 's16 s16_add'; break;
      case 'update': $class = 's16 s16_edit'; break;
      case 'delete': $class = 's16 s16_delete'; break;
      case 'error':  $class = 's16 s16_error'; break;
      default:       $class = '';
    }
    
    return '<span class="block '.$class.'">'.$action.'</span>';
  }
}