<?php

require_once(realpath(dirname(__FILE__).'/../../..').'/unit/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot('admin');

$t = new lime_test();

$domain = 'http://www.my-domain.com';
$sitemap = $helper->get('xml_sitemap_generator')->setOption('domain', $domain);
$cultures = $helper->get('i18n')->getCultures();

try
{
  $sitemap->execute();
  $t->pass('Sitemap generated');
}
catch(Exception $e)
{
  $t->fail('Sitemap generated: '.$e->getMessage());
}

$webDir = sfConfig::get('sf_web_dir');

$t->ok(file_exists($file = $webDir.'/sitemap.xml'), $file.' exists');

foreach($cultures as $culture)
{
  $t->ok(file_exists($file = $webDir.'/sitemap_'.$culture.'.xml'), $file.' exists');

  $t->like(
    file_get_contents($webDir.'/sitemap.xml'),
    '|<loc>'.preg_quote($domain.'/sitemap_'.$culture.'.xml', '|').'</loc>|',
    'sitemap.xml contains a reference to sitemap_'.$culture.'.xml'
  );
}

$sitemap->delete();

$t->ok(!file_exists($file = $webDir.'/sitemap.xml'), $file.' does no more exist');

foreach($cultures as $culture)
{
  $t->ok(!file_exists($file = $webDir.'/sitemap_'.$culture.'.xml'), $file.' no more exists');
}

$t->comment('With only one culture');

$helper->get('i18n')->setCultures(array('en'));

try
{
  $sitemap->execute();
  $t->pass('Sitemap generated');
}
catch(Exception $e)
{
  $t->fail('Sitemap generated: '.$e->getMessage());
}

$t->ok(file_exists($file = $webDir.'/sitemap.xml'), $file.' exists');

$t->ok(!file_exists($file = $webDir.'/sitemap_fr.xml'), $file.' does not exist');

$sitemap->delete();

$t->comment('With the task');

$helper->get('filesystem')->sf('dm:sitemap-update www.my-domain.com');

$t->ok(file_exists($file = $webDir.'/sitemap.xml'), $file.' exists');

foreach($cultures as $culture)
{
  $t->ok(file_exists($file = $webDir.'/sitemap_'.$culture.'.xml'), $file.' exists');

  $t->like(
    file_get_contents($webDir.'/sitemap.xml'),
    '|<loc>'.preg_quote($domain.'/sitemap_'.$culture.'.xml', '|').'</loc>|',
    'sitemap.xml contains a reference to sitemap_'.$culture.'.xml'
  );
}