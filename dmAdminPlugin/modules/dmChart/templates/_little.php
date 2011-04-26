<?php

if (!$sf_user->can($options['credentials']))
{
  return;
}

echo _tag('div.dm_box',
  _tag('div.title',
    _link('@dm_chart?name='.$chartKey)->set('.s16block.s16_arrow_up_right')->textTitle(__('Expanded view')).
    _tag('h2', __($options['name']))
  ).
  _tag('div.dm_box_inner.dm_data.m5.dm_auto_loading', array('json' => array(
    'url' => _link('@dm_chart?action=image&name='.$chartKey)->getHref()
  )))
);