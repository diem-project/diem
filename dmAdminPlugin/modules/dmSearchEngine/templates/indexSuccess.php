<?php

echo £o('div.dm_box.big.search_engine');

echo £('h1.title', __('Internal search engine'));

echo £o('div.dm_box_inner');

echo £('div.search_actions.clearfix',
  £('div.dm_third',
    £('h2.mb10', __('Search')).
    $form->open('method=get').
    $form['query']->label(null, '.mr10')->field()->error().
    $form->submit(__('Search')).
    $form->close()
  ).
  £('div.dm_third',
    £('h2.mb10', __('Index state')).
    get_partial('state', array('engine' => $engine))
  ).
  £('div.dm_third',
    £('h2.mb10', __('Index maintenance')).
    £link('dmSearchEngine/reload')->text(__('Reload index'))->set('.dm_medium_button')
  )
);

if ($query)
{
  echo £('p', __('Search completed in %1% ms', array('%1%' => $time)));
  include_partial('results', array('pager' => $pager));
}

echo £c('div');

echo £c('div');

if (isset($phpCli))
{
  echo £o('div.dm_box.big.search_engine');

  echo £('h1.title', __('Set up a cron to update the search index'));
  
  echo £o('div.dm_box_inner.documentation');
  
    echo £('p', __('Most UNIX and GNU/Linux systems allows for task planning through a mechanism known as cron. The cron checks a configuration file (a crontab) for commands to run at a certain time.'));
  
    echo £('p.mt10.mb10', __('Open %1% and add the line:', array('%1%' => '/etc/crontab')));
    
    echo £('code', £('pre', sprintf('@daily %s %s %s/symfony dm:search-update',
      $shellUser,
      $phpCli,
      $rootDir
    )));
    
    echo £('p.mt10', __('For more information on the crontab configuration file format, type man 5 crontab in a terminal.'));

  echo £c('div');
  
  echo £c('div');
}