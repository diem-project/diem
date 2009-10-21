<?php

class dmUserLogView extends dmLogView
{
  protected
  $rows = array(
    'time'     => 'renderTime',
    'user'     => 'renderUser',
    'browser'  => 'renderBrowser',
    'location' => 'renderLocation',
    'app'      => 'renderApp'
  );
  
  /*
   * Row renderers
   */
  
  protected function renderUser(dmUserLogEntry $entry)
  {
    return sprintf('%s%s<br /><span class=light>%s</span>',
      ($username = $entry->get('username')) ? sprintf('<strong class="mr10">%s</strong>', $username) : '',
      $entry->get('ip'),
      $entry->get('session_id')
    );
  }
  
  protected function renderBrowser(dmUserLogEntry $entry)
  {
    $browser = $entry->get('browser');
    return sprintf('<div class="clearfix"><div class="browser browser_block %s fleft"></div><strong class="mr10">%s %s</strong><span class="light">%s</span>',
      $browser->getName(),
      ucfirst($browser->getName()),
      $browser->getVersion(),
      str_replace('Linux', '<strong>Linux</strong>', $entry->get('user_agent'))
    );
  }
  
  protected function renderLocation(dmUserLogEntry $entry)
  {
    return sprintf('<span class="dm_nowrap">%s</span><br />%s<span class="light">%s ms</span>&nbsp;<span class="light">%s Mb</span>',
      $this->renderLink($entry),
      sprintf('<span class="s16 s16_%s">%s</span>',
        $entry->get('is_ok') ? 'status' : 'status_busy',
        $entry->get('is_ok') ? '' : $entry->get('code').' '
      ),
      $entry->get('timer'),
      round($entry->get('mem') / (1024*1024))
    );
  }
  
  protected function renderLink(dmUserLogEntry $entry)
  {
    $uri = ltrim($entry->get('uri'), '/');
    $text = $uri ? $uri : $entry->get('app').' home';
    
    return dmAdminLinkTag::build('app:'.$entry->get('app').'/'.$uri)->text($text);
  }
  
  protected function renderTime(dmUserLogEntry $entry)
  {
    return str_replace(' CEST', '', $this->dateFormat->format($entry->get('time')));
//    return date('Y/m/d H:m:s', $entry->get('time'));
  }
  
  protected function renderApp(dmUserLogEntry $entry)
  {
    return $entry->get('app');
  }
}