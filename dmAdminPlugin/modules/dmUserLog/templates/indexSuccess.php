<?php

echo £o('div.dm_box.big.log.user_log');

echo £('div.title',
  £link('dmUserLog/clear')->text(__('Clear')).
  £('h1', __('User log').sprintf(' ( %s )', dmOs::humanizeSize($filesize)))
);

echo £o('div.dm_box_inner.dm_data');

echo $view->render(200);

echo £c('div');

echo £c('div');