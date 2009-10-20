<?php

use_stylesheet('lib.ui-tabs');
use_javascript('lib.ui-tabs');
use_javascript('admin.chart');


echo £o('div.dm_charts.mt10');

echo £o('ul');
foreach($charts as $chartKey => $chart)
{
  echo £('li',
    £link('@dm_chart?action=show&name='.$chartKey)
    ->text(__($chart->getName()))
    ->set('.dm_chart_link'.($chartKey === $selectedChartKey ? '.selected' : ''))
  );
}
echo £c('ul');

echo £c('div');