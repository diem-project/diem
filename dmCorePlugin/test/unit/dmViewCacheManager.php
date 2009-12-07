<?php

require_once(dirname(__FILE__).'/helper/dmTestHelper.php');
$helper = new dmTestHelper();
$helper->boot();

sfConfig::set('sf_cache', true);

$t = new lime_test();

$cacheCleaner = $helper->get('cache_cleaner');

dm::loadHelpers(array('Partial'));

