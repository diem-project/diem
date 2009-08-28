<?php

echo

$form->renderGlobalErrors(),

£o('div.dm_tabbed_form'),

£('ul.tabs',
	£('li', £link('#'.$baseTabId.'_text')->text(__('Text'))).
	£('li', £link('#'.$baseTabId.'_media')->text(__('Media'))).
	£('li', £link('#'.$baseTabId.'_links')->text(__('Links'))).
  £('li', £link('#'.$baseTabId.'_advanced')->text(__('Advanced')))
),

£('div#'.$baseTabId.'_text',
	£('ul.dm_form_elements',
		$form['title']->renderRow().
		$form['text']->render(array('class' => 'dm_markdown'))
  )
),

£('div#'.$baseTabId.'_media',
  dmContext::getInstance()->getHelper()->renderPartial('dmWidget', 'forms/dmWidgetContentMedia', array(
    'form' => $form,
    'hasMedia' => $hasMedia
  ))
),

£('div#'.$baseTabId.'_links',
  £('ul.dm_form_elements',
    $form['titleLink']->renderRow().
    $form['mediaLink']->renderRow()
  )
),

£('div#'.$baseTabId.'_advanced',
  £('ul.dm_form_elements',
    $form['cssClass']->renderRow()
  )
),

£c('div'); //div.dm_tabbed_form