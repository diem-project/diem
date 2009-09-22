<?php
use_stylesheet('core.browsers');
use_stylesheet('admin.log');
use_javascript('admin.logs');

$userLog = £('div.dm_third',
  £('div.dm_box.log.user_log',
    £('div.title',
      £link('dmUserLog/index')->textTitle(__('Expanded view'))->set('.s16block.s16_arrow_up_right').
      £('h2', __('User log'))
    ).
    £('div.dm_box_inner',
      $userLogView->renderEmpty()
    )
  )
);

$actionLog = £('div.dm_third',
  £('div.dm_box.log.action_log.ml10',
    £('div.title',
      £link('dmActionLog/index')->textTitle(__('Expanded view'))->set('.s16block.s16_arrow_up_right').
      £('h2', __('Action log'))
    ).
    £('div.dm_box_inner',
      $actionLogView->renderEmpty()
    )
  )
);

echo £('h1', dmConfig::get('site_name'));

echo £('div.admin_home.clearfix', $userLog.$actionLog);