<?php

use_stylesheet('dmUserPlugin.myAccount');
use_javascript('dmUserPlugin.myAccount');

echo _open('div.dm_user_my_account');

echo _open('div.dm_box.little.mt10');

echo _tag('div.title',
  _tag('h2', $form->getObject())
);

echo _open('div.dm_box_inner');

echo $form->open('.dm_form.list.little');

echo _open('ul.dm_form_elements');

echo $form['email']->renderRow();

echo _tag('li.collapsible',
  _tag('a.collapsible_button', __('Change password')).
  _tag('ul.collapsible_content',
    $form['username']->field()->error().
    $form['old_password']->renderRow().
    $form['password']->renderRow().
    $form['password_again']->renderRow()
  )
);

echo $form->renderHiddenFields();

echo $form->submit(__('Save'));

echo $form->close();

echo _close('div');

echo _close('div');

echo _close('div');