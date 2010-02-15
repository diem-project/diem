<?php

$current = isset($current) ? $current : null;

echo _open('ul.dm_page_manager_list.ui-tabs-nav.ui-helper-reset.ui-helper-clearfix.ui-widget-header.ui-corner-all');

foreach(array('metas' => 'Manage metas', 'tree' => 'Reorder pages') as $action => $actionName)
{
  echo _tag('li.dm_page_manager_link.ui-state-default.ui-corner-top'.($action === $current ? '.ui-tabs-selected.ui-state-active' : ''),
    _link('dmPage/'.$action)->text(__($actionName))
  );
}

echo _close('ul');