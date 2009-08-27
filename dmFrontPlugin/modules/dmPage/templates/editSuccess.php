<?php

echo £('div.dm.dm_page_edit_wrap',

  $form->open('.dm_form.list.little').
  $form['id'].

  £('div.dm.dm_page_edit.dm_tabbed_form',

		£('ul.tabs',
		  £('li', £link('#dm_page_edit_seo')->name(__('Seo'))).
		  £('li', £link('#dm_page_edit_integration')->name(__('Integration'))).
      £('li', £link('#dm_page_edit_publication')->name(__('Publication')))
		).

    £('div#dm_page_edit_seo',
		  £('ul.dm_form_elements',
		    $form['slug']->renderRow().
		    $form['name']->renderRow().
		    $form['title']->renderRow().
		    $form['h1']->renderRow().
		    $form['description']->renderRow().
		    (isset($form['keywords']) ? $form['keywords']->renderRow() : '')
		  )
		).

		£('div#dm_page_edit_integration',
		  £('ul.dm_form_elements',
		    (isset($form['parent_id']) ? $form['parent_id']->renderRow() : '').
		    $form['dm_layout_id']->renderRow().
		    $form['module']->renderRow().
		    $form['action']->renderRow()
		  )
		).

    £('div#dm_page_edit_publication',
      £('ul.dm_form_elements',
        $form['is_approved']->renderRow().
        $form['is_secure']->renderRow()
      )
    )
  ).

  sprintf(
    '<div class="actions clearfix">%s%s</div>',
    sprintf('<a class="cancel dm close_dialog dm button fleft">%s</a>', __('Cancel')),
    sprintf('<input type="submit" class="submit and_save green fright" name="and_save" value="%s" />', __('Save'))
  ).
  
  sprintf("<div class='dm_seo_max_lengths %s'></div>",
    json_encode(sfConfig::get('dm_seo_truncate', array()))
  ).
  
  $form->close()

);

if ($css)
{
  echo sprintf('<style type="text/css">%s</style>', $css);
}

if ($js)
{
  echo '__DM_SPLIT__', $js;
}