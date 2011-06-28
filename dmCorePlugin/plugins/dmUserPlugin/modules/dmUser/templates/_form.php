<?php

if($sf_user->isAuthenticated())
{
  echo _tag('p', __('You are authenticated as %username%', array('%username%' => $sf_user->getUsername())));
  return;
}

echo $form->open('.dm_register_form');

echo _open('ul.dm_form_elements');

  echo _tag('li.dm_form_element', $form['username']->label()->field()->error());

  echo _tag('li.dm_form_element', $form['email']->label()->field()->error());

  echo _tag('li.dm_form_element', $form['password']->label()->field()->error());

  echo _tag('li.dm_form_element', $form['password_again']->label()->field()->error());

  // render captcha if enabled
  if($form->isCaptchaEnabled())
  {
    echo _tag('li.dm_form_element', $form['captcha']->label(null, 'for=false')->field()->error());
  }

echo _close('ul');

echo $form->renderHiddenFields();

echo $form->submit(__('Register'));

echo $form->close();