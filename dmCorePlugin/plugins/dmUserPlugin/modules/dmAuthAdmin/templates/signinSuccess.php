<?php

echo _open('div.dm.dm_auth');

echo _tag('h1.site_name', dmConfig::get('site_name'));

echo _tag('div.message',
  $form->open('.dm_form.list.little.clearfix action=+/dmAuthAdmin/signin').
    _tag('ul',
      _tag('li.dm_form_element.clearfix',
        $form['username']->error()->label(__('Username'))->field()
      ).
      _tag('li.dm_form_element.clearfix',
        $form['password']->error()->label(__('Password'))->field()
      )
    ).
    $form->submit(__('Login'), '.mt10').
  '</form>'
);

echo _close('div');

echo _link('http://diem-project.org/')->text('Diem CMF CMS for symfony')->set('.generator_link');

?>
<script type="text/javascript">document.getElementById('signin_username').focus();</script>