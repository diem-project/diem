<?php

$current = isset($current) ? $current : null;

echo _open('ul.dm_page_manager_list.ui-tabs-nav.ui-helper-reset.ui-helper-clearfix.ui-widget-header.ui-corner-all');

foreach(array('manageMetas' => 'Manage metas', 'reorderPages' => 'Reorder pages') as $action => $actionName)
{
  echo _tag('li.dm_page_manager_link.ui-state-default.ui-corner-top'.($action === $sf_request->getParameter('action') ? '.ui-tabs-selected.ui-state-active' : ''),
    _link('dmPage/'.$action)->text(__($actionName))
  );
}

echo _close('ul');