<?php

use_stylesheet('admin.dataTable');
use_stylesheet('admin.log');
use_javascript('admin.logs');

echo £('div.dm_box.log.action_log',
  £('div.title',
    £link('dmActionLog/index')->set('.s16block.s16_arrow_up_right')->textTitle(__('Expanded view')).
    £('h2', __('Action log'))
  ).
  £('div.dm_box_inner.dm_data', $log->renderEmpty())
);