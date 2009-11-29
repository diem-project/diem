<?php

echo £o('ul');

foreach($dm_layout->get('Areas') as $area)
{
  if (!$area->get('Zones')->count())
  {
    continue;
  }
  
  echo £o('li.mb5');
  echo £('strong', $area->get('type').':');
  
  echo £o('ul.ml10');
  
  foreach($area->get('Zones') as $zone)
  {
    foreach($zone->get('Widgets') as $widget)
    {
      echo £('li', __($sf_context->get('widget_type_manager')->getWidgetType($widget)->getPublicName()));
    }
  }
  
  echo £c('ul');
  echo £c('li');
}

echo £c('li');