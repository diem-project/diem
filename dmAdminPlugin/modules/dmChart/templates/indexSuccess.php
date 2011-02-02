<?php

echo _open('div.dm_charts.mt10', array('json' => array(
  'selected' => $selectedIndex
)));

echo _open('ul');
foreach($charts as $chartKey => $options)
{
  echo _tag('li',
    _link('@dm_chart?action=show&name='.$chartKey)
    ->text(__($options['name']))
    ->set('.dm_chart_link')
  );
}
echo _close('ul');

echo _close('div');