<?php

require_once(realpath(dirname(__FILE__).'/../../../..').'/functional/helper/dmFunctionalTestHelper.php');
$helper = new dmFunctionalTestHelper();
$helper->boot('admin');

$browser = $helper->getBrowser();

$helper->logout();

$browser->get('/')
->with('response')->begin()
->isStatusCode(200)
->checkElement('.unsupported_browser', true)
->end()
->with('request')->begin()
->isParameter('module', 'dmAdmin')
->isParameter('action', 'index')
->end()
;

$browser->click('Or continue at your own peril')
->with('response')->begin()
->isStatusCode(401)
->checkElement('form.dm_form')
->end()
->with('request')->begin()
->isParameter('module', 'dmUserAdmin')
->isParameter('action', 'signin')
->end()
;

$helper->login();