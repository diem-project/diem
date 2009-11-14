<?php

//echo $form->renderGlobalErrors();

echo £o('li.toggle_group');

echo $form['mediaId']->render(array('class' => 'dm_media_id'));

if ($hasMedia)
{
  echo £('a.show_media_fields.toggler', __('Change file'));
}

echo £('ul.media_fields'.($hasMedia ? '.none' : ''),
  $form['mediaName']->renderRow().
  $form['file']->renderRow()
);

echo £c('li');


if ($hasMedia)
{
  echo
  $form['legend']->renderRow().
  £('li.dm_form_element.multi_inputs.thumbnail.clearfix',
    $form['width']->renderError().
    $form['height']->renderError().
    £('label', __('Dimensions')).
    $form['width']->render().
    'x'.
    $form['height']->render().
    $form['method']->label(null, array('class' => 'ml10 mr10 fnone'))->field('.dm_media_method')->error()
  ).
  £('li.dm_form_element.multi_inputs.background.clearfix.none',
    $form['width']->renderError().
    $form['background']->label()->field()->error()
  );
}

if (!isset($skipCssClass))
{
  echo $form['cssClass']->renderRow();
}