<?php

require_once(realpath(dirname(__FILE__).'/dmEventLogView.php'));

class dmEventLogViewLittle extends dmEventLogView
{
  protected
  $rows = array(
//    'time'     => 'renderTime',
    'user'     => 'renderUserTime',
    'action'   => 'renderActionAndSubject'
  );

  protected function renderUserTime(dmEventLogEntry $entry)
  {
    $username = $entry->get('username');
    
    return ($username ? '<strong class="mr10">'.dmString::escape(dmString::truncate($username, 20, '...')).'</strong><br />' : '').$entry->get('ip');
  }
  
  protected function renderActionAndSubject(dmEventLogEntry $entry)
  {
    return '<span class="block '.$this->getActionClass($entry->get('action')).'">'.$this->renderType($entry).'<br />'.$this->renderSubject($entry).'</span>';
  }
}