<?php

echo

$form->renderGlobalErrors(),

_open('div.dm_tabbed_form'),

_tag('ul.tabs',
  _tag('li', _link('#'.$baseTabId.'_text')->text(__('Text'))).
  _tag('li', _link('#'.$baseTabId.'_media')->text(__('Media'))).
  _tag('li', _link('#'.$baseTabId.'_links')->text(__('Links'))).
  _tag('li', _link('#'.$baseTabId.'_advanced')->text(__('Presentation')))
),

_tag('div#'.$baseTabId.'_text',
  _tag('ul.dm_form_elements',
    $form['title']->renderRow().
    $form['text']->render(array('class' => 'dm_markdown'))
  )
),

_tag('div#'.$baseTabId.'_media',
  $sf_context->get('helper')->renderPartial('dmWidget', 'forms/dmWidgetContentImage', array(
    'form' => $form,
    'hasMedia' => $hasMedia,
    'skipCssClass' => true
  ))
),

_tag('div#'.$baseTabId.'_links',
  _tag('ul.dm_form_elements',
    _tag('li.dm_form_element.clearfix',
      $form['titleLink']
      ->label(__('Title'))->field()->error().
      _tag('p.dm_help', __('Add a link to the text title').'<br />'.__('Drag & Drop a page or enter an url'))
    ).    
    _tag('li.dm_form_element.clearfix',
      $form['titleLinkTarget']
      ->label(__('Target'))->field()->error().
      _tag('p.dm_help', __('Target window for title link'))
    ).  
    _tag('li.dm_form_element.clearfix',
      $form['mediaLink']
      ->label(__('Media'))->field()->error().
      _tag('p.dm_help', __('Add a link to the text media').'<br />'.__('Drag & Drop a page or enter an url'))
    ).
    _tag('li.dm_form_element.clearfix',
      $form['mediaLinkTarget']
      ->label(__('Target'))->field()->error().            
      _tag('p.dm_help', __('Target window for media link'))
    )      
          
  )
),

_tag('div#'.$baseTabId.'_advanced',
  _tag('ul.dm_form_elements',
    $form['cssClass']->renderRow().
    $form['titlePosition']->renderRow()
  )
),

_close('div'); //div.dm_tabbed_form