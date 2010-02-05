<?php

$helper->logout();

$browser->get('/+/dmCore/refresh?skip_browser_detection=1')
->checks(array(
  'moduleAction' => 'dmCore/refresh',
  'code' => '401'
));

$helper->login();

$scriptName = $helper->getService('request')->getScriptName();

$browser->get('/+/dmCore/refresh')
->checks(array(
  'moduleAction' => 'dmCore/refresh',
))
->has('p.title', 'Updating project')
->get('/+/dmCore/refreshStep/step/1')
->checks(array(
  'moduleAction' => 'dmCore/refreshStep',
));

/*
 * With some old version of sqlite, like on continuous integration server
 * This test will not work as expected
 */

if(strpos(getcwd(), 'hudson'))
{
  return;
}

$browser->testResponseContent('{"msg":"Page synchronization","type":"ajax","url":"\\'.$scriptName.'\\/+\\/dmCore\\/refreshStep?step=2"}')
->get('/+/dmCore/refreshStep/step/2')
->checks(array(
  'moduleAction' => 'dmCore/refreshStep',
))
->testResponseContent('{"msg":"SEO synchronization","type":"ajax","url":"\\'.$scriptName.'\\/+\\/dmCore\\/refreshStep?step=3"}')
->get('/+/dmCore/refreshStep/step/3')
->checks(array(
  'moduleAction' => 'dmCore/refreshStep',
))
->testResponseContent('|\{"msg"\:"Interface regeneration","type"\:"redirect","url"\:".+"\}|', 'like')
;