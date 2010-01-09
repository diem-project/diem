<?php
// Main : Login form

echo $form->open();

echo £('ul',

  £('li', $form['username']->label()->field()->error()).

  £('li', $form['password']->label()->field()->error()).

  £('li', $form['remember']->label()->field()->error())

);

echo $form->submit('Login');

echo $form->close();