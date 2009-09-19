<?php

require_once(realpath(dirname(__FILE__).'/dmActionLogView.php'));

class dmActionLogViewLittle extends dmActionLogView
{
  protected
  $rows = array(
    'time'     => 'renderTime',
    'user'     => 'renderUserLittle',
    'action'   => 'renderActionAndSubject'
  );

  protected function renderUserLittle(dmActionLogEntry $entry)
  {
    return ($entry->get('username') ? sprintf('<strong class="mr10">%s</strong>', $entry->get('username')) : '').$entry->get('ip');
  }
  
  protected function renderActionAndSubject(dmActionLogEntry $entry)
  {
    return $this->renderAction($entry).' '.$this->renderSubject($entry);
  }
}