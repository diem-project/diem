<?php

$current = isset($current) ? $current : null;

echo £o('div.dm_auto_seo_manager.ui-tabs.ui-widget.ui-widget-content.ui-corner-all.mt10');

echo £o('ul.dm_auto_seo_list.ui-tabs-nav.ui-helper-reset.ui-helper-clearfix.ui-widget-header.ui-corner-all');

echo £('li.help.dm_auto_seo_link.ui-state-default.ui-corner-top'.(null == $current ? '.ui-tabs-selected.ui-state-active' : ''),
  £link('@dm_auto_seo')->text('Referencing automatic pages')
);

foreach($autoSeos as $autoSeo)
{
  echo £('li.dm_auto_seo_link.ui-state-default.ui-corner-top'.($autoSeo === $current ? '.ui-tabs-selected.ui-state-active' : ''),
    £link($autoSeo)->text(__($autoSeo->getTargetDmModule()->getPlural()))
  );
}

echo £c('ul');