<?php

echo _tag('h1', dmConfig::get('site_name'));

if($checkVersion)
{
  echo _tag('div#dm_async_version_check');
}

echo $homepageManager->render();