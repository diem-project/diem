<?php

echo

$form->renderGlobalErrors(),

£o('div.dm_tabbed_form'),

£('ul.tabs',
  £('li', £link('#'.$baseTabId.'_items')->text(__('Items'))).
  £('li', £link('#'.$baseTabId.'_advanced')->text(__('Advanced')))
),

£('div#'.$baseTabId.'_items.drop_zone',
  £('ol.items_list', array('json' => array(
    'items' => $items,
    'delete_message' => __('Remove this item')
  )), '').
  £('div.dm_help.no_margin', __('Drag & drop links here from the left PAGE panel'))
),

£('div#'.$baseTabId.'_advanced',
  £('ul.dm_form_elements',
    $form['cssClass']->renderRow()
  )
),

£c('div'); //div.dm_tabbed_form