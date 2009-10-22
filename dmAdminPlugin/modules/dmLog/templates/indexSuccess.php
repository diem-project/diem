<?php

echo £o('div.dm_logs.mt10', array('json' => array(
  'selected' => $selectedIndex
)));

echo £o('ul');
foreach($logs as $logKey => $log)
{
  echo £('li',
    £link('@dm_log?action=show&name='.$logKey)
    ->text(__($log->getName()))
    ->set('.dm_log_link')
  );
}
echo £c('ul');

echo £c('div');