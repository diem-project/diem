<?php

$current = isset($current) ? $current : null;

echo _open('ul.dm_auto_seo_list.ui-tabs-nav.ui-helper-reset.ui-helper-clearfix.ui-widget-header.ui-corner-all');

foreach($autoSeos as $autoSeo)
{
  echo _tag('li.dm_auto_seo_link.ui-state-default.ui-corner-top'.($autoSeo === $current ? '.ui-tabs-selected.ui-state-active' : ''),
    _link($autoSeo)->text(__($autoSeo->getTargetDmModule()->getPlural()))
  );
}

echo _close('ul');