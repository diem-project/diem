<?php

echo £o('ul');

foreach($dm_layout->get('Areas') as $area)
{
  if (!$area->get('Zones')->count())
  {
    continue;
  }

  $widgets = array();
  foreach($area->get('Zones') as $zone)
  {
    foreach($zone->get('Widgets') as $widget)
    {
      $widgets[] = __($sf_context->get('widget_type_manager')->getWidgetType($widget)->getPublicName());
    }
  }

  if(!empty($widgets))
  {
    echo £('li.mb5',
      £('strong', $area->type.': ').
      implode(' | ', $widgets)
    );
  }
}

echo £c('li');