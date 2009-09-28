<?php

require_once(realpath(dirname(__FILE__).'/dmActionLogView.php'));

class dmActionLogViewLittle extends dmActionLogView
{
  protected
  $rows = array(
//    'time'     => 'renderTime',
    'user'     => 'renderUserTime',
    'action'   => 'renderActionAndSubject'
  );

  protected function renderUserTime(dmActionLogEntry $entry)
  {
    $username = $entry->get('username');
    
    return ($username ? '<strong class="mr10">'.$username.'</strong><br />' : '').$entry->get('ip');
  }
  
  protected function renderActionAndSubject(dmActionLogEntry $entry)
  {
    return '<span class="block '.$this->getActionClass($entry->get('action')).'">'.$this->renderType($entry).'<br />'.$this->renderSubject($entry).'</span>';
  }
}