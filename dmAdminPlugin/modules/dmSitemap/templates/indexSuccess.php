<?php use_helper('Date');

$form = sprintf('%s%s%s',
  sprintf('<form action="%s">', £link('@dm_sitemap?action=generate')->getHref()),
  sprintf('<input type="submit" value="%s" />', __('Generate sitemap')),
  '</form>'
);

echo £o('div.dm_box.big.sitemap');

echo £('h1.title', __('Generate sitemap'));

echo £o('div.dm_box_inner');

if ($exists)
{
  echo £('div.clearfix.mb10',
    definition_list(array(
      'Position' => £link($webPath),
      'Urls' => $nbLinks,
      'Size' => $size,
      'Updated at' => format_date($updatedAt)
    ), '.clearfix.dm_little_dl.fleft.mr20').
    $form
  );
  
  echo £('pre', array('style' => 'background: #fff; padding: 10px; border: 1px solid #ddd; max-height: 350px; overflow-y: auto;'), htmlentities($xml, ENT_QUOTES, 'UTF-8'));
}
else
{
  echo £('p', __('There is currently no sitemap'));
  
  echo $form;
}

echo £c('div');

echo £c('div');

if (isset($phpCli))
{
  echo £o('div.dm_box.big.search_engine');

  echo £('h1.title', __('Set up a cron to update the sitemap'));
  
  echo £o('div.dm_box_inner.documentation');
  
    echo £('p', __('Most UNIX and GNU/Linux systems allows for task planning through a mechanism known as cron. The cron checks a configuration file (a crontab) for commands to run at a certain time.'));
  
    echo £('p.mt10.mb10', __('Open %1% and add the line:', array('%1%' => '/etc/crontab')));
    
    echo £('code', £('pre', sprintf('@daily %s %s %s/symfony dm:sitemap-update %s',
      $shellUser,
      $phpCli,
      $rootDir,
      $domainName
    )));
    
    echo £('p.mt10', __('For more information on the crontab configuration file format, type man 5 crontab in a terminal.'));

  echo £c('div');
  
  echo £c('div');
}