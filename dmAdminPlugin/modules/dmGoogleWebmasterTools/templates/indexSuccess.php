<?php

echo £o('div.dm_google_webmaster_tools.seo_service');

  echo £link('https://www.google.com/webmasters/tools/home?hl='.$sf_user->getCulture())
  ->set('.dm_big_button')
  ->target('blank')
  ->text(__('Open Google Webmaster Tools page'));
  
  if(isset($form))
  {
    echo £o('div.dm_box.little.mt40');

    echo £('div.title',
      £('h2', __('Configure Google Webmaster Tools'))
    );
    
    echo £('div.dm_box_inner',
      $form->open('.dm_form.list.little').
      $form['gwt_key']->renderRow().
      $form->renderSubmitTag(__('Save')).
      $form->close()
    );
    
    echo £c('div');
  }
  
echo £c('div');