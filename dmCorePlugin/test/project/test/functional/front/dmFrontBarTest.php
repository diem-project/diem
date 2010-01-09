<?php

require_once(realpath(dirname(__FILE__).'/../../../..').'/functional/helper/dmFunctionalTestHelper.php');
$helper = new dmFunctionalTestHelper();
$helper->boot('front');

$browser = $helper->getBrowser();

$helper->logout();

$browser->get('/')->followRedirect()
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

$browser->get('/')->followRedirect()
->with('response')->begin()
->isStatusCode(200)
->checkElement('#dm_page_bar')
->checkElement('#dm_page_bar_toggler')
->checkElement('#dm_media_bar')
->checkElement('#dm_media_bar_toggler')
->checkElement('#dm_tool_bar')
->checkElement('#dm_tool_bar .dm_add_menu')
->checkElement('#dm_tool_bar .dm_add_menu .zone_add', 'Zone')
->end()
->with('request')->begin()
->isParameter('module', 'dmFront')
->isParameter('action', 'page')
->end()
;

$browser->get('/+/dmInterface/loadPageTree')
->with('response')->begin()
->isStatusCode(200)
->end()
->with('request')->begin()
->isParameter('module', 'dmInterface')
->isParameter('action', 'loadPageTree')
->end()
->test()->like($browser->getResponse()->getContent(), '#^'.preg_quote('{"html":"<ul><li id=\"dmp1\"><a class=\"s16 s16_page_manual\" href=\"\">', '#').'#');
;

$browser->get('/+/dmInterface/loadMediaFolder')
->with('response')->begin()
->isStatusCode(200)
->end()
->with('request')->begin()
->isParameter('module', 'dmInterface')
->isParameter('action', 'loadMediaFolder')
->end()
->test()->like($browser->getResponse()->getContent(), '#^'.preg_quote('<div class="breadCrumb"><a', '#').'#');
;