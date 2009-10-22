<?php

use_stylesheet('core.browsers');
use_stylesheet('admin.dataTable');
use_stylesheet('admin.log');
use_javascript('admin.logs');

echo £('div.dm_box.log.'.$logKey.'_log',
  £('div.title',
    £link('@dm_log')->param('name', $logKey)->set('.s16block.s16_arrow_up_right')->textTitle(__('Expanded view')).
    £('h2', __($log->getName()))
  ).
  £('div.dm_box_inner.dm_data', $logView->renderEmpty())
);