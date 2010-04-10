<?php

echo $form->open('.dm_forgot_password_form');

echo _open('ul.dm_form_elements');

  echo _tag('li.dm_form_element', $form['email']->label()->field()->error());

  // render captcha if enabled
  if($form->isCaptchaEnabled())
  {
    echo _tag('li.dm_form_element', $form['captcha']->label(null, 'for=false')->field()->error());
  }

echo _close('ul');

echo $form->renderHiddenFields();

echo $form->submit(__('Receive a new password'));

echo $form->close();