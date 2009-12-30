<style type="text/css">
  ul.choices li {
    margin: 30px;
    font-size: 1.5em;
    list-style: square inside;
    color: #ccc;
  }
</style>

<?php

echo £('div.dm_box.little.secure.mt20', £('div.title', £('h2', 'You don\'t have the required permission to access this page.')).£('div.dm_box_inner',
  £('ul.choices',
    £('li', £link('dmAuth/signout')->text(__('Logout'))).
    £('li', £link('@homepage')->text(__('Back to admin homepage'))).
    £('li', £link('app:front')->text(__('Back to front homepage')))
  )
));