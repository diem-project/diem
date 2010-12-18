<?php

require_once(realpath(dirname(__FILE__).'/../../../..').'/functional/helper/dmFunctionalTestHelper.php');
$helper = new dmFunctionalTestHelper();
$helper->boot('front');

$browser = $helper->getBrowser();

$helper->logout();

$browser->get('/')
->with('response')->begin()
->isStatusCode(200)
->checkElement('#dm_page_bar', false)
->checkElement('#dm_page_bar_toggler', false)
->checkElement('#dm_media_bar', false)
->checkElement('#dm_media_bar_toggler', false)
->checkElement('#dm_tool_bar', false)
->end()
->with('request')->begin()
->isParameter('module', 'dmFront')
->isParameter('action', 'page')
->end()
;

$helper->login();

$browser->get('/')
->with('response')->begin()
->isStatusCode(200)
->checkElement('#dm_page_bar')
->checkElement('#dm_page_bar_toggler')
->checkElement('#dm_media_bar')
->checkElement('#dm_media_bar_toggler')
->checkElement('#dm_tool_bar')
->checkElement('#dm_tool_bar .dm_add_menu')
->end()
->with('request')->begin()
->isParameter('module', 'dmFront')
->isParameter('action', 'page')
->end()
;

require_once(realpath(dirname(__FILE__).'/..').'/dmBarFunctionalTestInclude.php');