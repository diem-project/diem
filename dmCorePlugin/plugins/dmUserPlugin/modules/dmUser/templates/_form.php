<?php

echo $form->open('.dm_register_form');

echo _tag('ul',

  _tag('li', $form['username']->label()->field()->error()).

  _tag('li', $form['email']->label()->field()->error()).

  _tag('li', $form['password']->label()->field()->error()).

  _tag('li', $form['password_again']->label()->field()->error())

);

echo $form->renderHiddenFields();

echo $form->submit(__('Register'));

echo $form->close();