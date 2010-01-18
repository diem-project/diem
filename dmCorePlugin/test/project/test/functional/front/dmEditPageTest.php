<?php

require_once(realpath(dirname(__FILE__).'/../../../..').'/functional/helper/dmFunctionalTestHelper.php');
$helper = new dmFunctionalTestHelper();
$helper->boot('front');

$b = $helper->getBrowser();

$helper->login();

$b
->get('/page11')
->checks(array('page_module_action' => 'main/page11'))
->editPage()
->updatePage(array())
->editPage()
->updatePage(array(
  'slug' => 'new-slug',
  'name' => 'New name',
  'title' => 'New title',
  'description' => 'New description',
  'keywords' => 'New keywords'
))
->checks(array('page_module_action' => 'main/page11'))
->has('title', 'New title | Project')
->testResponseContent('|<meta name="description" content="New description" />|', 'like')
->testResponseContent('|<meta name="keywords" content="New keywords" />|', 'like')

->get('/index.php')
->checks(array('page_module_action' => 'main/root'))
->editPage()
->updatePage(array(
  'h1' => 'New h1',
))
->checks(array('page_module_action' => 'main/root'))
->has('h1', 'New h1')
;