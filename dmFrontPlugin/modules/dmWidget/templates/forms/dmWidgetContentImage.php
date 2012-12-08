<?php

//echo $form->renderGlobalErrors();

echo _open('div.toggle_group');

echo $form['mediaId']->render(array('class' => 'dm_media_id'));

if ($hasMedia)
{
  echo _tag('a.show_media_fields.toggler', __('Change file'));
}

echo _tag('ul.media_fields'.($hasMedia ? '.none' : ''),
  $form['mediaName']->renderRow().
  $form['file']->renderRow().
  (isset($form['removeMedia']) ? $form['removeMedia']->renderRow() : '')
);

echo _close('div');

if ($hasMedia)
{
  echo
  _tag('ul.dm_form_elements',
    $form['legend']->renderRow().
    _tag('li.dm_form_element.multi_inputs.thumbnail.clearfix',
      $form['width']->renderError().
      $form['height']->renderError().
      _tag('label', __('Dimensions')).
      $form['width']->render().
      'x'.
      $form['height']->render().
      $form['method']->label(null, array('class' => 'ml10 mr10 fnone'))->field('.dm_media_method')->error()
    ).
    _tag('li.dm_form_element.multi_inputs.background.clearfix.none',
      $form['background']->renderError().
      $form['background']->label()->field()->error()
    ).
    $form['quality']->renderRow().
    (isset($form['link']) ? $form['link']->renderRow(array('class' => 'dm_link_droppable')).$form['target']->renderRow() : '')
  );
}

if (!isset($skipCssClass))
{
  echo $form['cssClass']->renderRow();
}