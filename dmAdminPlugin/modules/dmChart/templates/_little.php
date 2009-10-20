<?php

echo £('div.dm_box',
  £('div.title',
    £link('@dm_chart?name='.$chartKey)
    ->set('.s16block.s16_arrow_up_right')
    ->textTitle(__('Expanded view')).
    £('h2', __($chart->getName()))
  ).
  £('div.dm_box_inner.dm_data.m5', $image
  ? £link('@dm_chart?name='.$chartKey)->text($image->htmlWidth('100%'))->title(__('Expanded view'))
  : __('This chart is currently not available')
  )
);