<?php
use_stylesheet('core.browsers');
use_stylesheet('admin.userLog');

echo £o('div.dm_box.big.user_log');

echo £('div.title',
  £link('dmUserLog/clear')->text(__('Clear')).
  £('h1', __('User log'))
);

echo £o('div.dm_box_inner');

echo $view->render(200);

echo £c('div');

echo £c('div');