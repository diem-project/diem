<?php

require_once(realpath(dirname(__FILE__).'/dmRequestLogView.php'));

class dmRequestLogViewLittle extends dmRequestLogView
{
  protected
  $rows = array(
//    'time'     => 'renderTime',
    'user'     => 'renderUserAndBrowser',
    'location' => 'renderLocation',
  );
  
  protected function renderUserAndBrowser(dmRequestLogEntry $entry)
  {
    $browser = $entry->get('browser');
    return sprintf('<div class="browser %s">%s%s<br />%s %s</div>',
      $browser->getName(),
      ($username = $entry->get('username')) ? sprintf('<strong class="mr5">%s</strong>', $username) : '',
      $entry->get('ip'),
      ucfirst($browser->getName()),
      $browser->getVersion()
    );
  }
}