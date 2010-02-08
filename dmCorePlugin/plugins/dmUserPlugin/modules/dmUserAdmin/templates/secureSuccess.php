<?php

echo _open('div.dm.dm_auth.secure');

echo _tag('h1.site_name', dmConfig::get('site_name'));

echo _tag('div.message', __("You don't have the required permission to access this page."));

echo _tag('ul.choices',
  _tag('li', _link('@signout')->text(__('Signout'))).
  ($sf_user->can('admin') ? _tag('li', _link('@homepage')->text(__('Back to admin'))) : '').
  _tag('li', _link('app:front')->text(__('Back to site')))
);

echo _close('div');

echo _link('http://diem-project.org/')->text('Diem CMF CMS for symfony')->set('.generator_link');