<?php

echo £o('div.dm.dm_auth');

echo £('h1.site_name', dmConfig::get('site_name'));

echo £('div.message',
  $form->open('.dm_form.list.little.clearfix action=+/dmAuth/signin').
    £('ul',
      £('li.dm_form_element.clearfix',
        $form['username']->error()->label(__('Username'))->field()
      ).
      £('li.dm_form_element.clearfix',
        $form['password']->error()->label(__('Password'))->field()
      )
    ).
    $form->submit(__('Login'), '.mt10').
  '</form>'
);

echo £c('div');

echo £link('http://diem-project.org/')->text('Diem CMF CMS for symfony')->set('.generator_link');

?>
<script type="text/javascript">document.getElementById('signin_username').focus();</script>