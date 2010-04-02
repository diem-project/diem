<?php

require_once(realpath(dirname(__FILE__).'/dmRequestLogView.php'));

class dmRequestLogViewLittle extends dmRequestLogView
{
  protected
  $rows = array(
    'user'     => 'renderUserAndBrowser',
    'location' => 'renderLocation',
  );
  
  protected function renderUserAndBrowser(dmRequestLogEntry $entry)
  {
    $browser = $entry->get('browser');
    return sprintf('<div class="browser %s">%s<br />%s %s</div>',
      $this->getBrowserIcon($browser),
      ($username = $entry->get('username'))
      ? '<strong class="mr5">'.dmString::escape(dmString::truncate($username, 20, '...')).'</strong>'
      : $this->renderIp($entry->get('ip')),
      ucfirst($browser->getName()),
      $browser->getVersion()
    );
  }
}