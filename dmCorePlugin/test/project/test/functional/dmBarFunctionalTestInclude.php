<?php

$browser->get('/+/dmInterface/loadPageTree')
->checks(array(
  'moduleAction' => 'dmInterface/loadPageTree',
))
->has('li:first li li', 'Page 21')
->get('/+/dmInterface/loadMediaFolder')
->checks(array(
  'moduleAction' => 'dmInterface/loadMediaFolder',
))
->has('ul.content.clearfix')
->has('li.folder');

$browser->test()->like($browser->getResponse()->getContent(), '#^'.preg_quote('<div class="breadCrumb"><a', '#').'#', 'Response seems well formed');
$browser->test()->unlike($browser->getResponse()->getContent(), '#(warning|error|exception)#', 'Response does not contain an error message');