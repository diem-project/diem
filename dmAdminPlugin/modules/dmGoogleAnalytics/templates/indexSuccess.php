<?php

echo _open('div.dm_google_analytics.seo_service');

  echo _link('https://www.google.com/analytics/settings/?et=reset&hl='.$sf_user->getCulture())
  ->set('.dm_big_button')
  ->target('blank')
  ->text(__('Open Google Analytics page'));
  
  if(isset($form))
  {
    echo _open('div.dm_box.little.mt40');

    echo _tag('div.title',
      _tag('h2', __('Configure Google Analytics'))
    );
    
    echo _tag('div.dm_box_inner',
      $form->open('.dm_form.list.little').
      _tag('li', _tag('h3', __('Send reports'))).
      $form['key']->renderRow().
      _tag('li.separator', '&nbsp;').
      _tag('li', _tag('h3', __('Receive reports'))).
      _tag('div.mb10 style="text-align: center"',
        ($gapiConnected
        ? _tag('span.s16.s16_tick', __('Connected'))
        : _tag('span.s16.s16_cross', __('Not connected')))
      ).
      $form['email']->renderRow().
      $form['password']->renderRow().
      $form->renderSubmitTag(__('Save')).
      $form->close()
    );
    
    echo _close('div');
  }
  
echo _close('div');