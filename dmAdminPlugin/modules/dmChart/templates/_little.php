<?php

if (!$sf_user->can($chart->getCredentials()))
{
  return;
}

echo £('div.dm_box',
  £('div.title',
    £link('@dm_chart?name='.$chartKey)
    ->set('.s16block.s16_arrow_up_right')
    ->textTitle(__('Expanded view')).
    £('h2', __($chart->getName()))
  ).
  £('div.dm_box_inner.dm_data.m5.dm_auto_loading', array('json' => array(
    'url' => £link('@dm_chart?action=image&name='.$chartKey)->getHref(),
    'height' => 200
  )))
);