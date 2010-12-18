<?php

echo _open('div.dm_google_webmaster_tools.seo_service');

  echo _link('https://www.google.com/webmasters/tools/home?hl='.$sf_user->getCulture())
  ->set('.dm_big_button')
  ->target('blank')
  ->text(__('Open Google Webmaster Tools page'));
  
  if(isset($form))
  {
    echo _open('div.dm_box.little.mt40');

    echo _tag('div.title',
      _tag('h2', __('Configure Google Webmaster Tools'))
    );
    
    echo _tag('div.dm_box_inner',
      $form->open('.dm_form.list.little').
      $form['gwt_key']->renderRow().
      $form->renderSubmitTag(__('Save')).
      $form->close()
    );
    
    echo _close('div');
  }
  
echo _close('div');