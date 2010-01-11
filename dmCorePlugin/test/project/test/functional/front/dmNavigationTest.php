<?php

require_once(realpath(dirname(__FILE__).'/../../../..').'/functional/helper/dmFunctionalTestHelper.php');
$helper = new dmFunctionalTestHelper();
$helper->boot('front');

$b = $helper->getBrowser();

$b->get('/index.php')
->checks(array(
  'code' => 200,
  'module_action' => 'dmFront/page',
  'h1' => 'Project'
))
->has('.dm_widget_navigation_bread_crumb')
->has('body.global_layout');