<?php

require_once(dirname(__FILE__).'/helper/dmPageUnitTestHelper.php');
$helper = new dmPageUnitTestHelper();
$helper->boot();

$t = new lime_test(1);

$t->diag('page seo tests');

try
{
  $helper->get('seo_synchronizer')->execute(array(), sfConfig::get('sf_default_culture'));
  $t->pass('seo_synchronizer service executed successfully');
}
catch(Exception $e)
{
  $t->fail('seo_synchronizer service executed successfully');
}