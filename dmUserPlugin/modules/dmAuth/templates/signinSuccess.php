<?php

echo £o('div.dm.dm_auth');

echo £('h1.site_name', dmConfig::get('site_name'));

echo £('div.message',
  $form->open('.dm_form.list.little.clearfix action=+/dmAuth/signin').
    $form->renderGlobalErrors().
    £('ul',
      £('li.dm_form_element.clearfix',
        $form['username']->renderError().
        $form['username']->renderLabel(__('Username')).
        $form['username']->render()
      ).
      £('li.dm_form_element.clearfix',
        $form['password']->renderError().
        $form['password']->renderLabel(__('Password')).
        $form['password']->render()
      )
    ).
    $form->renderSubmitTag(__('Login'), 'button blue fright mt10').
    $form['remember'].
  '</form>'
);

echo £c('div');

?>
<script type="text/javascript">document.getElementById('signin_username').focus();</script>