<?php

if($sf_user->isAuthenticated())
{
  echo _tag('p', __('You are authenticated as %username%', array('%username%' => $sf_user->getUsername())));
  return;
}

if($email = $sf_user->getFlash('dm_forgot_password_email_sent'))
{
  echo _tag('p', __('A link to change your password has been sent to %email%', array('%email%' => $email)));
  echo _link('main/signin');
  return;
}

if($sf_user->getFlash('dm_forgot_password_changed'))
{
  echo _tag('p', __('Your password has been changed'));
  echo _link('main/signin');
  return;
}

// step 1: request a new password by giving an email
if($step == 1)
{
  include_partial('dmUser/forgotPasswordStep1', array('form' => $form));
}
// step 2: the mail has been received, now choose the new password
else
{
  include_partial('dmUser/forgotPasswordStep2', array('form' => $form));
}