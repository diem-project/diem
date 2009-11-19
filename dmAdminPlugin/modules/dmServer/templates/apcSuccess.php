<style type="text/css">
  iframe {
    width: 100%;
    height: 500px;
  }
</style>
<?php

echo £o('div.dm_box.big.apc_monitor');

echo £('h1.title', __('Apc monitor'));

echo £o('div.dm_box_inner');

echo £('iframe src='.£link('dmServer/includeApc')->getHref());

echo £c('div');

echo £c('div');