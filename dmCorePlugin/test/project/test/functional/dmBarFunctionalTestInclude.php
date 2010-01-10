<?php

$browser->get('/+/dmInterface/loadPageTree')
->with('response')->begin()
->isStatusCode(200)
->end()
->with('request')->begin()
->isParameter('module', 'dmInterface')
->isParameter('action', 'loadPageTree')
->end();

$browser->test()->like($browser->getResponse()->getContent(), '#^'.preg_quote('{"html":"<ul><li id=\"dmp1\"><a class=\"s16 s16_page_manual\" href=\"\">', '#').'#', 'Response seems well formed');
//$browser->test()->unlike($browser->getResponse()->getContent(), '#(warning|error|exception)#', 'Response does not contain an error message');

$content = $browser->getResponse()->getContent();
$data = json_decode($content, true);

$browser->test()->ok(isset($data['html']), 'Response contains HTML');
$browser->test()->ok(isset($data['js']), 'Response contains JS');

$browser->get('/+/dmInterface/loadMediaFolder')
->with('response')->begin()
->isStatusCode(200)
->checkElement('ul.content.clearfix')
->checkElement('li.folder')
->checkElement('li.file')
->end()
->with('request')->begin()
->isParameter('module', 'dmInterface')
->isParameter('action', 'loadMediaFolder')
->end();

$browser->test()->like($browser->getResponse()->getContent(), '#^'.preg_quote('<div class="breadCrumb"><a', '#').'#', 'Response seems well formed');
$browser->test()->unlike($browser->getResponse()->getContent(), '#(warning|error|exception)#', 'Response does not contain an error message');