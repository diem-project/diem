<?php

echo _tag('div.dm.dm_page_edit_wrap',

  $form->open('.dm_form.list.little').
  $form['id'].

  _tag('div.dm.dm_page_edit.dm_tabbed_form',

    _tag('ul.tabs',
      _tag('li', _link('#dm_page_edit_seo')->text(__('Seo'))).
      _tag('li', _link('#dm_page_edit_integration')->text(__('Integration'))).
      _tag('li', _link('#dm_page_edit_publication')->text(__('Publication')))
    ).

    _tag('div#dm_page_edit_seo',
      _tag('ul.dm_form_elements',
        $form['slug']->renderRow().
        $form['name']->renderRow().
        $form['title']->renderRow().
        $form['h1']->renderRow().
        $form['description']->renderRow().
        (isset($form['keywords']) ? $form['keywords']->renderRow() : '')
      )
    ).

    _tag('div#dm_page_edit_integration',
      _tag('ul.dm_form_elements',
        (isset($form['parent_id']) ? $form['parent_id']->renderRow() : '').
        $form['dm_layout_id']->renderRow().
        $form['module']->renderRow().
        $form['action']->renderRow()
      )
    ).
    _tag('div#dm_page_edit_publication',
      _tag('ul.dm_form_elements',
        $form['is_active']->renderRow().
        $form['is_secure']->renderRow().
        _tag('li.dm_form_element.credentials.clearfix'.($page->isSecure ? '' : '.none'),
          $form['credentials']->label()->field()->error()
        ).
        $form['is_indexable']->renderRow()
      )
    )
  ).

  sprintf(
    '<div class="actions clearfix">%s%s%s</div>',
    sprintf('<a class="cancel dm close_dialog button fleft">%s</a>', __('Cancel')),
    $deletePageLink ? _link('+/dmPage/delete')->param('id', $page->get('id'))->set('.dm.delete.button.red.ml10.left.dm_js_confirm')->text(__('Delete'))->title(__('Delete this page')) : '',
    sprintf('<input type="submit" class="submit and_save green fright" name="and_save" value="%s" />', __('Save'))
  ).
  
  sprintf("<div class='dm_seo_max_lengths %s'></div>",
    json_encode(sfConfig::get('dm_seo_truncate', array()))
  ).
  
  $form->close()
);