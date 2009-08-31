<?php
use_stylesheet('core.browsers');
use_stylesheet('admin.userLog');
use_javascript('admin.userLog');

echo £('h1', $site->name);

echo £('div.admin_home.clearfix',

  £('div.dm_half',
    £('div.dm_box.user_log', array('json' => $userLogOptions),
      £('div.title',
        £link('dmUserLog/index')->textTitle(__('Expanded view'))->set('.s16block.s16_arrow_up_right').
        £('h2', __('User log'))
      ).
      £('div.dm_box_inner',
        $userLogView->renderEmpty()
      )
    )
  )
);