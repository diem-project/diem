<?php

echo £('h1', dmConfig::get('site_name'));

echo £('div.clearfix',

  £('div.dm_third',
    get_component('dmChart', 'little', array('name' => 'week')).
    get_component('dmChart', 'little', array('name' => 'content')).
    get_component('dmChart', 'little', array('name' => 'browser'))
  ).

  £('div.dm_third',
    get_component('dmChart', 'little', array('name' => 'visit')).
    get_component('dmChart', 'little', array('name' => 'log'))
  ).

  £('div.dm_third',
    get_component('dmLog', 'little', array('name' => 'request')).
    get_component('dmLog', 'little', array('name' => 'event'))
  )
  
);