<?php

echo _open('div.dm_box.big.search_engine');

echo _tag('h1.title', __('Internal search engine'));

echo _open('div.dm_box_inner');

echo _tag('div.search_actions.clearfix',
  _tag('div.dm_third',
    _tag('h2.mb10', __('Search')).
    $form->open('method=get').
    $form['query']->label(null, '.mr10')->field()->error().
    $form->submit(__('Search')).
    $form->close()
  ).
  _tag('div.dm_third',
    _tag('h2.mb10', __('Index state')).
    get_partial('state', array('engine' => $engine))
  ).
  _tag('div.dm_third',
    _tag('h2.mb10', __('Index maintenance')).
    _link('dmSearchEngine/reload')
     ->text(__('Reload index'))
     ->set('.dm_medium_button')
     ->set('onclick', '$("div.search_actions").block();')
  )
);

if ($query)
{
  echo _tag('p', __('Search completed in %1% ms', array('%1%' => $time)));
  include_partial('results', array('pager' => $pager));
}

echo _close('div');

echo _close('div');

if (isset($phpCli))
{
  echo _open('div.dm_box.big.search_engine');

  echo _tag('h1.title', __('Set up a cron to update the search index'));
  
  echo _open('div.dm_box_inner.documentation');
  
    echo _tag('p', __('Most UNIX and GNU/Linux systems allows for task planning through a mechanism known as cron. The cron checks a configuration file (a crontab) for commands to run at a certain time.'));
  
    echo _tag('p.mt10.mb10', __('Open %1% and add the line:', array('%1%' => '/etc/crontab')));
    
    echo _tag('code', _tag('pre', sprintf('@daily %s %s %s/symfony dm:search-update',
      $shellUser,
      $phpCli,
      $rootDir
    )));
    
    echo _tag('p.mt10', __('For more information on the crontab configuration file format, type man 5 crontab in a terminal.'));

  echo _close('div');
  
  echo _close('div');
}