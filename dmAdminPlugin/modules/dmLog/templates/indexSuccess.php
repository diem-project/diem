<?php

echo _open('div.dm_logs.mt10', array('json' => array(
  'selected' => $selectedIndex
)));

echo _open('ul');
foreach($logs as $logKey => $log)
{
  echo _tag('li',
    _link('@dm_log?action=show&name='.$logKey)
    ->text(__($log->getName()))
    ->set('.dm_log_link')
  );
}
echo _close('ul');

echo _close('div');