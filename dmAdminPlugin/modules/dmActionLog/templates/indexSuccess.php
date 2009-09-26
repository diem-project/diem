<?php

echo £o('div.dm_box.big.log.action_log');

echo £('div.title',
  £link('dmActionLog/clear')->text(__('Clear')).
  £('h1', __('Action log').sprintf(' ( %s )', dmOs::humanizeSize($filesize)))
);

echo £o('div.dm_box_inner.dm_data');

echo $view->render(200);

echo £c('div');

echo £c('div');