<?php

echo $form->open('.dm_forgot_password_form');

echo _open('ul.dm_form_elements');

  echo _tag('li.dm_form_element', $form['password']->label()->field()->error());

  echo _tag('li.dm_form_element', $form['password_again']->label()->field()->error());

echo _close('ul');

echo $form->renderHiddenFields();

echo _tag('input type=hidden value='.$form->getUser()->forgot_password_code);

echo $form->submit(__('Save the new password'));

echo $form->close();