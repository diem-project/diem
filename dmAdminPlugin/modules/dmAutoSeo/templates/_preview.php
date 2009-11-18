<?php

echo £o('div.dm_auto_seo_preview');

if (!$page)
{
  echo £('div.alert', 'There is no page to preview');
}
elseif (isset($metas))
{
  echo £o('ul');
  
  foreach($metas as $key => $value)
  {
    echo £('li.dm_meta_preview', $value);
  }
  
  echo £c('ul');
}
else
{
  echo __('The configuration is not valid.');
}

echo £c('div');