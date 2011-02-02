<?php

require_once(realpath(dirname(__FILE__).'/../../../..').'/functional/helper/dmFunctionalTestHelper.php');
$helper = new dmFunctionalTestHelper();
$helper->boot('admin');

$b = $helper->getBrowser();

$helper->login();

$b->get('/tools/configuration/configuration-panel/index')
->checks(array(
  'code' => 200,
  'moduleAction' => 'dmConfigPanel/index',
  'h1' => 'Configuration panel')
);