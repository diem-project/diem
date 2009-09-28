<?php
use_stylesheet('core.browsers');
use_stylesheet('admin.dataTable');
use_stylesheet('admin.log');
use_javascript('admin.logs');

echo £('div.dm_third',
  £('div.dm_box.log.user_log',
    £('div.title',
      £link('dmUserLog/index')->set('.s16block.s16_arrow_up_right')->textTitle(__('Expanded view')).
      £('h2', __('User log'))
    ).
    £('div.dm_box_inner.dm_data', $userLogView->renderEmpty())
  )
);