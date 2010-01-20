<?php

require_once(realpath(dirname(__FILE__).'/../../../..').'/functional/helper/dmFunctionalTestHelper.php');
$helper = new dmFunctionalTestHelper();
$helper->boot('front');

$browser = $helper->getBrowser();

require_once(realpath(dirname(__FILE__).'/..').'/dmRefreshFunctionalTestInclude.php');