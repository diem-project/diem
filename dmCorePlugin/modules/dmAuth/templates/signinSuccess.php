<?php

echo £o('div.dm.dm_auth');

echo £('h1.site_name', dmConfig::get('site_name'));

echo £('div.message',
  £('p', __("You don't have the required permission to access this page.")).
  $form->open('.dm_form.list.little.clearfix action=dmAuth/signin').
    $form->renderGlobalErrors().
    £('ul',
      £('li.dm_form_element.clearfix',
		    $form['username']->renderError().
		    $form['username']->renderLabel(__('Username')).
		    $form['username']->render(array('class' => 'hint'))
		  ).
      £('li.dm_form_element.clearfix',
        $form['password']->renderError().
        $form['password']->renderLabel(__('Password')).
        $form['password']->render(array('class' => 'hint'))
      )
    ).
    $form->renderSubmitTag(__('Login'), 'button blue fright mt10').
    $form['remember'].
  '</form>'
);

echo £c('div');