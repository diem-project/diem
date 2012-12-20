<?php

echo

$form->renderGlobalErrors(),

_open('div.dm_tabbed_form'),

_tag('ul.tabs',
  _tag('li', _link('#'.$baseTabId.'_items')->text(__('Items'))).
  _tag('li', _link('#'.$baseTabId.'_advanced')->text(__('Advanced')))
),

_tag('div#'.$baseTabId.'_items.drop_zone',
  _tag('ol.items_list', array('json' => array(
    'items' => $items,
    'extended_show_message' => __('Show extended options'),
    'extended_hide_message' => __('Hide extended options'),
    'delete_message' => __('Remove'),
    'text_message' => __('Text'),
    'link_message' => __('Link'),
    'secure_message' => __('Requires authentication'),
    'nofollow_message' => __('No follow'),
    'depth_message' => __('Depth'),
    'target_message' => __('Target'),
    'click_message' => __('Click to edit, drag to sort')
  )), '').
  _tag('div.dm_help.no_margin',
    __('Drag & drop links here from the left PAGE panel').
    '<br />'.
    _tag('a.external_link', __('or create an external link'))
  )
),

_tag('div#'.$baseTabId.'_advanced',
  _tag('ul.dm_form_elements',
    $form['cssClass']->renderRow().
    $form['ulClass']->renderRow().
    $form['menuName']->renderRow().
    $form['liClass']->renderRow().
    (isset($form['menuClass']) ? $form['menuClass']->renderRow() : '')
  )
),

_close('div'); //div.dm_tabbed_form
