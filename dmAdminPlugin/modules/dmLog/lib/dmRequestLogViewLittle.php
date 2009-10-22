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
  
  protected function renderLocation(dmRequestLogEntry $entry)
  {
    return sprintf('%s<br />%s',
      $this->renderLink($entry),
      sprintf('<span class="s16 s16_%s">%s<span class="light">%s ms</span></span>',
        $entry->get('is_ok') ? 'status' : 'status_busy',
        $entry->get('is_ok') ? '' : sprintf('<strong class="mr10">%s</strong>', $entry->get('code')),
        $entry->get('timer')
      )
    );
  }
}