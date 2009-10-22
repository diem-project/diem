<?php

echo £o('div.dm_charts.mt10', array('json' => array(
  'selected' => $selectedIndex
)));

echo £o('ul');
foreach($charts as $chartKey => $chart)
{
  echo £('li',
    £link('@dm_chart?action=show&name='.$chartKey)
    ->text(__($chart->getName()))
    ->set('.dm_chart_link')
  );
}
echo £c('ul');

echo £c('div');