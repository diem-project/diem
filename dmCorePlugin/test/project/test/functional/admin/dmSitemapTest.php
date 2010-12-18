<?php

require_once(realpath(dirname(__FILE__).'/../../../..').'/functional/helper/dmFunctionalTestHelper.php');
$helper = new dmFunctionalTestHelper();
$helper->boot('admin');

$b = $helper->getBrowser();

$helper->login();

$b->get('/seo/sitemap/manage-xml-sitemap/index')
->checks(array(
  'code' => 200,
  'moduleAction' => 'dmSitemap/index',
  'h1' => 'Manage XML sitemap')
)
->click('Generate sitemap')
->checks(array(
  'method' => 'post',
  'code' => 302,
  'moduleAction' =>  'dmSitemap/generate'
))
->redirect()
->checks()
->has('.dm_sitemap_tabs li:first', 'sitemap.xml')
->has('.dm_sitemap_tabs li:eq(1)', 'sitemap_en.xml')
->has('.dm_sitemap_tabs li:last', 'sitemap_fr.xml')
->has('.dm_sitemap pre');