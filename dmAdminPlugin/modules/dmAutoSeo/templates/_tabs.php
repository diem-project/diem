<?php

$current = isset($current) ? $current : null;

$links = array();
foreach($autoSeos as $autoSeo)
{
  $name = __($autoSeo->getTargetDmModule()->getPlural());

  if('show' !== $autoSeo->action)
  {
    $name .= '('.__($autoSeo->action).')';
  }

  $links[$name] = _tag('li.dm_auto_seo_link.ui-state-default.ui-corner-top'.($autoSeo === $current ? '.ui-tabs-selected.ui-state-active' : ''),
    _link($autoSeo)->text($name)
  );
}

ksort($links);

echo _tag('ul.dm_auto_seo_list.ui-tabs-nav.ui-helper-reset.ui-helper-clearfix.ui-widget-header.ui-corner-all',
  implode('', $links)
);