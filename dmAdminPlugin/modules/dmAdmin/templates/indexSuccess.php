<?php

echo £('h1', dmConfig::get('site_name'));

if($checkVersion)
{
  echo £('div#dm_async_version_check');
}

echo $homepageManager->render();