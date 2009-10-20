<?php

echo £('div.dm_chart.text_align_center',
  $image
  ? $image->alt(__($chart->getName()))
  : __('This chart is currently not available')
);