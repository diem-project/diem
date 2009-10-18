<?php

echo £('div.dm_google_analytics.seo_service',

  £link('https://www.google.com/analytics/settings/?et=reset&hl='.$sf_user->getCulture())
  ->set('.dm_big_button')
  ->target('blank')
  ->text('Open Google Analytics page').
  
  (isset($form)
  ? $form->open().
  $form['ga_key']->renderRow().
  $form->renderSubmitTag(__('Save')).
  $form->close()
  : '')
  
);