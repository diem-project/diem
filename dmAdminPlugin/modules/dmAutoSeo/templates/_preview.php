<?php

echo _open('div.dm_auto_seo_preview');

if (!$page)
{
  echo _tag('div.alert', 'There is no page to preview');
}
elseif (isset($metas))
{
  echo _open('ul');
  
  foreach($metas as $key => $value)
  {
    echo _tag('li.dm_meta_preview', $value);
  }
  
  echo _close('ul');
}
else
{
  echo __('The configuration is not valid.');
}

echo _close('div');