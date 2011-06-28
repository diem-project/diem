<?php

echo _tag('div.dm_chart.text_align_center',
  !empty($image)
  ? $image->alt(__($chart->getName()))
  : __('This chart is currently not available')
);