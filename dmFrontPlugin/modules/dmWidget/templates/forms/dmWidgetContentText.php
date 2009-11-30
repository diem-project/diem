<?php

echo

$form->renderGlobalErrors(),

£o('div.dm_tabbed_form'),

£('ul.tabs',
  £('li', £link('#'.$baseTabId.'_text')->text(__('Text'))).
  £('li', £link('#'.$baseTabId.'_media')->text(__('Media'))).
  £('li', £link('#'.$baseTabId.'_links')->text(__('Links'))).
  £('li', £link('#'.$baseTabId.'_advanced')->text(__('Presentation')))
),

£('div#'.$baseTabId.'_text',
  £('ul.dm_form_elements',
    $form['title']->renderRow().
    $form['text']->render(array('class' => 'dm_markdown'))
  )
),

£('div#'.$baseTabId.'_media',
  $sf_context->get('helper')->renderPartial('dmWidget', 'forms/dmWidgetContentMedia', array(
    'form' => $form,
    'hasMedia' => $hasMedia,
    'skipCssClass' => true
  ))
),

£('div#'.$baseTabId.'_links',
  £('ul.dm_form_elements',
    £('li.dm_form_element.clearfix',
      $form['titleLink']
      ->label(__('Title'))
      ->field('.dm_link_droppable')
      ->error().
      £('p.dm_help', __('Add a link to the text title').'<br />'.__('Drag & Drop a page or enter an url'))
    ).
    £('li.dm_form_element.clearfix',
      $form['mediaLink']
      ->label('Media')
      ->field('.dm_link_droppable')
      ->error().
      £('p.dm_help', __('Add a link to the text media').'<br />'.__('Drag & Drop a page or enter an url'))
    )
  )
),

£('div#'.$baseTabId.'_advanced',
  £('ul.dm_form_elements',
    $form['cssClass']->renderRow().
    $form['titlePosition']->renderRow()
  )
),

£c('div'); //div.dm_tabbed_form