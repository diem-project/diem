<?php

echo £('div.dm_google_webmaster_tools.seo_service',

  £link('https://www.google.com/webmasters/tools/home?hl='.$sf_user->getCulture())
  ->set('.dm_big_button')
  ->target('blank')
  ->text('Open Google Webmaster Tools page').
  
  (isset($form)
  ? $form->open().
  $form['gwt_key']->renderRow().
  $form->renderSubmitTag(__('Save')).
  $form->close()
  : '')
  
);