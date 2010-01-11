<?php

require_once(realpath(dirname(__FILE__).'/../../../..').'/functional/helper/dmFunctionalTestHelper.php');
$helper = new dmFunctionalTestHelper();
$helper->boot('front');

$b = $helper->getBrowser();

$b->get('/')
->checks(array(
  'code' => 200,
  'module_action' => 'dmFront/page',
  'h1' => 'Home'
))
->has('.dm_widget_navigation_bread_crumb');