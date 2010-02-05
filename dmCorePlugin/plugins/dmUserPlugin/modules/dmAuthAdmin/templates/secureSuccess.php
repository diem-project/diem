<style type="text/css">
  ul.choices li {
    margin: 30px;
    font-size: 1.5em;
    list-style: square inside;
    color: #ccc;
  }
</style>

<?php

echo _tag('div.dm_box.little.secure.mt20', _tag('div.title', _tag('h2', __("You don't have the required permission to access this page.")))._tag('div.dm_box_inner',
  _tag('ul.choices',
    _tag('li', _link('dmAuthAdmin/signout')->text(__('Logout'))).
    _tag('li', _link('@homepage')->text(__('Back to admin homepage'))).
    _tag('li', _link('app:front')->text(__('Back to front homepage')))
  )
));