<?php

if($sf_user->isAuthenticated())
{
  echo _tag('p', __('You are authenticated as %username%', array('%username%' => $sf_user->getUsername())));
  return;
}

if($email = $sf_user->getFlash('dm_new_password_sent'))
{
  echo _tag('p', __('A new password has been sent to %email%', array('%email%' => $email)));
  echo _link('main/signin');
  return;
}

echo $form->open('.dm_forgot_password_form');

echo _tag('ul.dm_form_elements',

  _tag('li.dm_form_element', $form['email']->label()->field()->error())

);

echo $form->renderHiddenFields();

echo $form->submit(__('Receive a new password'));

echo $form->close();