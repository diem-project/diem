<?php
// Main : Login form

echo $form->open();

echo _tag('ul',

  _tag('li', $form['username']->label()->field()->error()).

  _tag('li', $form['password']->label()->field()->error()).

  _tag('li', $form['remember']->label()->field()->error())

);

echo $form->renderHiddenFields().$form->submit('Login');

echo $form->close();