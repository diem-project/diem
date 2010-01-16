<?php
use_helper('Date');
use_stylesheet('lib.ui-tabs');
use_javascript('lib.ui-tabs');
use_javascript('admin.sitemap');

echo £o('div.dm_sitemap.mt10');

echo sprintf('%s%s%s',
  sprintf('<form style="text-align: center" method="post" class="dm_sitemap_generate_form" action="%s">', £link('@dm_sitemap?action=generate')->getHref()),
  sprintf('<input type="submit" value="%s" />', __('Generate sitemap')),
  '</form>'
);

echo £o('div.dm_sitemap_tabs.mt10');

echo £o('ul');
foreach($sitemap->getFiles() as $file)
{
  echo £('li', £('a href=#dm_sitemap_'.dmString::slugify(basename($file)), basename($file)));
}
echo £c('ul');

foreach($sitemap->getFiles() as $file)
{
  echo £o('div#dm_sitemap_'.dmString::slugify(basename($file)));

  if (file_exists($file))
  {
    echo £('div.clearfix.mb10',
      definition_list(array(
        'Position' => £link($sitemap->getWebPath($file)),
        'Urls' => $sitemap->countUrls($file),
        'Size' => $sitemap->getFileSize($file),
        'Updated at' => format_date($sitemap->getUpdatedAt($file))
      ), '.clearfix.dm_little_dl.fleft.mr20')
    );

    echo £('pre', array('style' => 'background: #fff; padding: 10px; border: 1px solid #ddd; max-height: 350px; overflow-y: auto;'), htmlentities(file_get_contents($file), ENT_QUOTES, 'UTF-8'));
  }
  else
  {
    echo sprintf('<input type="submit" class="dm_sitemap_generate" value="%s" />', __('Generate sitemap'));
  }

  if(!is_writable($file))
  {
    echo £('p.error', __('File %1% is not writable', array('%1%' => dmProject::unrootify($file))));
  }

  echo £c('div');
}

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

echo £c('div');