<?php

echo £o('div.dm_google_analytics.seo_service');

  echo £link('https://www.google.com/analytics/settings/?et=reset&hl='.$sf_user->getCulture())
  ->set('.dm_big_button')
  ->target('blank')
  ->text('Open Google Analytics page');
  
  if(isset($form))
  {
    echo £o('div.dm_box.little.mt40');

    echo £('div.title',
      £('h2', __('Configure Google Analytics'))
    );
    
    echo £('div.dm_box_inner',
      $form->open('.dm_form.list.little').
      £('li', £('h3', 'Send reports')).
      $form['ga_key']->renderRow().
      £('li.separator', '&nbsp;').
      £('li', £('h3', 'Receive reports')).
      $form['ga_email']->renderRow().
      $form['ga_password']->renderRow().
      $form->renderSubmitTag(__('Save')).
      $form->close()
    );
  }
  
echo £c('div');