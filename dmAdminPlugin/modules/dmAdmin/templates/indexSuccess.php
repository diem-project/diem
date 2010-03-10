<?php

echo _tag('h1', dmConfig::get('site_name'));

if($checkVersion)
{
  echo _tag('div#dm_async_version_check');
}

if($reportAnonymousData)
{
  echo _tag('div#dm_async_report');
}

echo $homepageManager->render();