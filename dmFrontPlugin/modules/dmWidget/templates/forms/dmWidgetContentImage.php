<?php

//echo $form->renderGlobalErrors();

echo £o('div.toggle_group');

echo $form['mediaId']->render(array('class' => 'dm_media_id'));

if ($hasMedia)
{
  echo £('a.show_media_fields.toggler', __('Change file'));
}

echo £('ul.media_fields'.($hasMedia ? '.none' : ''),
  $form['mediaName']->renderRow().
  $form['file']->renderRow()
);

echo £c('div');

if ($hasMedia)
{
  echo
  £('ul.dm_form_elements',
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
    ).
    £('li.dm_form_element.quality.clearfix',
      $form['quality']->label(__('JPG quality'))->field()->error().
      £('p.dm_help', __('Leave empty to use default quality'))
    )
  );
}

if (!isset($skipCssClass))
{
  echo $form['cssClass']->renderRow();
}