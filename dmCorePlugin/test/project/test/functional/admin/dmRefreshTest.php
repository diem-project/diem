<?php

require_once(realpath(dirname(__FILE__).'/../../../..').'/functional/helper/dmFunctionalTestHelper.php');
$helper = new dmFunctionalTestHelper();
$helper->boot('admin');

$browser = $helper->getBrowser();

require_once(realpath(dirname(__FILE__).'/..').'/dmRefreshFunctionalTestInclude.php');