<?php

echo _open('div.view_part');

foreach($area->getDmZones() as $zone)
{
  echo get_partial('dmZone/show', array('zone' => $zone));
}

echo _close('div');