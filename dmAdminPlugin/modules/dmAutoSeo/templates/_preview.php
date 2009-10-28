<?php

echo £o('div.dm_auto_seo_preview');

if (!$page)
{
  echo £('div.alert', 'There is no page to preview');
}
else
{
  echo £o('ul');
  
  foreach($metas as $key => $value)
  {
    echo £('li.dm_meta_preview', $value);
  }
  
  echo £c('ul');
}

echo £c('div');