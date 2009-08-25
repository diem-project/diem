<?php use_helper('Form', 'I18N');

echo £o('div.dm.dm_auth');

echo £('h1.site_name', __($site->getName()));

echo £('div.message',
  £('p', __("You don't have the required permission to access this page.")).
  form_tag('dmAuth/signin', array('class' => 'dm_form list little clearfix')).
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
    submit_tag(__('Login'), array('class' => 'button blue fright mt10')).
    $form['remember'].
  '</form>'
);

echo £c('div');