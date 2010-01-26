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
    'delete_message' => __('Remove'),
    'text_message' => __('Text'),
    'link_message' => __('Link'),
    'click_message' => __('Click to edit')
  )), '').
  £('div.dm_help.no_margin', __('Drag & drop links here from the left PAGE panel'))
),

£('div#'.$baseTabId.'_advanced',
  £('ul.dm_form_elements',
    $form['cssClass']->renderRow().
    $form['ulClass']->renderRow().
    $form['liClass']->renderRow()
  )
),

£c('div'); //div.dm_tabbed_form