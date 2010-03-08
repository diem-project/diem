<?php

echo $form->open('action=@signin .dm_signin_form');

echo _tag('ul',

  _tag('li', $form['username']->label()->field()->error()).

  _tag('li', $form['password']->label()->field()->error()).

  _tag('li', $form['remember']->label()->field()->error())

);

echo $form->renderHiddenFields();

echo $form->submit(__('Signin'));

echo $form->close();