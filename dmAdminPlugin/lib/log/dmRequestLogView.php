<?php

class dmRequestLogView extends dmLogView
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
  
  protected function renderUser(dmRequestLogEntry $entry)
  {
    return sprintf('%s%s',
      ($username = $entry->get('username')) ? sprintf('<strong class="mr10">%s</strong><br />', dmString::escape(dmString::truncate($username, 20, '...'))) : '',
      $this->renderIp($entry->get('ip'))
    );
  }
  
  protected function renderBrowser(dmRequestLogEntry $entry)
  {
    $browser = $entry->get('browser');

    return sprintf('<div class="clearfix"><div class="browser browser_block %s fleft"></div><strong class="mr10">%s %s</strong><span class="light">%s</span>',
      $this->getBrowserIcon($browser),
      ucfirst($browser->getName()),
      $browser->getVersion(),
      str_replace('Linux', '<strong>Linux</strong>', dmString::escape($entry->get('user_agent')))
    );
  }

  protected function getBrowserIcon($browser)
  {
    if(in_array($browser->getName(), array('googlebot', 'yahoobot', 'msnbot')))
    {
      $icon = $browser->getName(). ' browser_bot';
    }
    else
    {
      $icon = $browser->getName();
    }

    return $icon;
  }
  
  protected function renderLocation(dmRequestLogEntry $entry)
  {
    return sprintf('<span class="dm_nowrap">%s</span><br />%s<span class="light">%s ms</span>&nbsp;<span class="light">%s Mb</span>%s',
      $this->renderLink($entry),
      sprintf('<span class="s16 s16_%s">%s</span>',
        'status_'.$entry->getStatus(),
        $entry->renderCodeOrNull().' '
      ),
      $entry->get('timer'),
      round($entry->get('mem') / (1024*1024)),
      $entry->get('cache')
      ? '<span class="s16 s16_lightning_small"></span>'
      : ''
    );
  }
  
  protected function renderLink(dmRequestLogEntry $entry)
  {
    $uri = ltrim($entry->get('uri'), '/');
    $text = $uri ? $uri : $entry->get('app').' home';
    
    return $this->helper->link('app:'.$entry->get('app').'/'.$uri)->text(dmString::escape($text));
  }
  
  protected function renderTime(dmRequestLogEntry $entry)
  {
    return str_replace(' CEST', '', $this->dateFormat->format($entry->get('time')));
  }
  
  protected function renderApp(dmRequestLogEntry $entry)
  {
    $env = $entry->get('env');
    
    return $entry->get('app').('prod' !== $env ? ' '.$env : '');
  }
  
  protected function doGetEntries(array $options)
  {
    return $this->log->getEntries($this->maxEntries, array_merge($options, array('filter' => array($this, 'filterEntry'))));
  }
  
  public function filterEntry(array $data)
  {
    if (!empty($data['xhr']))
    {
      return (dmRequestLogEntry::isAlert($data) || dmRequestLogEntry::isError($data)) && $this->user->can('error_log');
    }
    
    return true;
  }
}