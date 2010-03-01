<?php

echo _open('ul');

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
      try
      {
        $widgetType = $sf_context->get('widget_type_manager')->getWidgetType($widget);
        $widgets[] = __($widgetType->getPublicName());
      }
      catch(dmException $e)
      {
        $widgets[] = $e->getMessage();
      }
    }
  }

  if(!empty($widgets))
  {
    echo _tag('li.mb5',
      _tag('strong', __(ucfirst($area->type)).': ').
      implode(' | ', $widgets)
    );
  }
}

echo _close('li');