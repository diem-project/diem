<?php

require_once(realpath(dirname(__FILE__).'/../../../..').'/functional/helper/dmFunctionalTestHelper.php');
$helper = new dmFunctionalTestHelper();
$helper->boot('front');

$b = $helper->getBrowser();

$helper->login();

$b
->get('/index.php')
->checks()
->info('Add a zone')
->get(sprintf('/index.php/+/dmZone/add?to_dm_area=%d&dm_cpi=%d', $b->getPage()->PageView->Area->id, $b->getPage()->id))
->checks(array('module_action' => 'dmZone/add', 'method' => 'get'))
->get('/index.php')
->addWidget($b->getPage()->PageView->Area->Zones[1], 'dmWidgetContent/title')
->editWidget($b->getPage()->PageView->Area->Zones[1]->Widgets[0])
->has('label:first', 'Text')
->updateWidget($b->getPage()->PageView->Area->Zones[1]->Widgets[0], array(
  'text' => 'Title 1',
  'tag' => 'h1',
  'cssClass' => 'test_title_class'
))
->get('/index.php')
->checks()
->has('.test_title_class', 'Title 1')
->info('Edit the title widget')
->editWidget($b->getPage()->PageView->Area->Zones[1]->Widgets[0])
->has('label:first', 'Text')
->updateWidget($b->getPage()->PageView->Area->Zones[1]->Widgets[0], array(
  'text' => 'Title 1 modified',
  'tag' => 'h1',
  'cssClass' => 'test_title_class'
))
->get('/index.php')
->checks()
->has('.test_title_class', 'Title 1 modified')
->addWidget($b->getPage()->PageView->Area->Zones[1], 'dmWidgetContent/link')
->editWidget($b->getPage()->PageView->Area->Zones[1]->Widgets[1])
->has('label:first', 'Href')
->updateWidget($b->getPage()->PageView->Area->Zones[1]->Widgets[1], array(
  'href' => 'page:'.dmDb::table('DmPage')->findOneByModuleAndAction('dmTestDomain', 'list')->id,
  'cssClass' => 'test_link_class'
))
->get('/index.php')
->checks()
->has('.test_link_class', 'Dm test domains')
->click('.test_link_class')
->checks(array('h1' => 'Domains'))
->back()
->addWidget($b->getPage()->PageView->Area->Zones[1], 'dmWidgetContent/text')
->editWidget($b->getPage()->PageView->Area->Zones[1]->Widgets[2])
->has('label:first', 'Title')
->updateWidget($b->getPage()->PageView->Area->Zones[1]->Widgets[2], array(
  'title' => 'Text title',
  'text' => '**Text body**',
  'cssClass' => 'test_text_class'
))
->get('/index.php')
->checks()
->has('.test_text_class h2', 'Text title')
->has('.test_text_class strong', 'Text body')
->editWidget($b->getPage()->PageView->Area->Zones[1]->Widgets[2])
->updateWidget($b->getPage()->PageView->Area->Zones[1]->Widgets[2], array(
  'title' => 'Text title',
  'text' => '**Text body**',
  'file' => sfConfig::get('sf_upload_dir').'/default.jpg',
  'width' => 333,
  'height' => 222,
  'legend' => 'Media legend',
  'titleLink' => 'page:'.dmDb::table('DmPage')->findOneByModuleAndAction('dmTestDomain', 'list')->id,
  'mediaLink' => 'page:'.dmDb::table('DmPage')->findOneByModuleAndAction('dmTestDomain', 'list')->id,
  'cssClass' => 'test_text_class'
))
->get('/index.php')
->checks()
->has('.test_text_class h2', 'Text title')
->has('.test_text_class strong', 'Text body')
->has('.test_text_class .text_image img')
->with('response')->begin()
->matches('|<img alt="Media legend" height="222" src="/uploads/widget/.thumbs/default_[\w\d]+.jpg" width="333" />|')
->end()
->click('.test_text_class .text_title .link')
->checks(array('h1' => 'Domains'))
->back()
->click('.test_text_class .text_image .link')
->checks(array('h1' => 'Domains'))
->back()
->addWidget($b->getPage()->PageView->Area->Zones[1], 'dmWidgetContent/image')
->editWidget($b->getPage()->PageView->Area->Zones[1]->Widgets[3])
->has('label:first', 'Use media')
->updateWidget($b->getPage()->PageView->Area->Zones[1]->Widgets[3], array(
  'file' => sfConfig::get('sf_upload_dir').'/default.jpg',
  'width' => 333,
  'height' => 222,
  'legend' => 'Media legend 2',
  'cssClass' => 'test_image_class'
))
->get('/index.php')
->checks()
->has('.test_image_class img')
->with('response')->begin()
->matches('|<img alt="Media legend 2" height="222" src="/uploads/widget/.thumbs/default_[\w\d]+.jpg" width="333" />|')
->end()
->get('/dm-test-domains/domain-1/categ-1/post-1')
->info('Add a zone')
->get(sprintf('/index.php/+/dmZone/add?to_dm_area=%d&dm_cpi=%d', $b->getPage()->PageView->Area->id, $b->getPage()->id))
->checks(array('module_action' => 'dmZone/add', 'method' => 'get'))
->get('/dm-test-domains/domain-1/categ-1/post-1')
->addWidget($b->getPage()->PageView->Area->Zones[1], 'dmWidgetNavigation/breadCrumb')
->editWidget($b->getPage()->PageView->Area->Zones[1]->Widgets[0])
->has('label:first', 'Separator')
->updateWidget($b->getPage()->PageView->Area->Zones[1]->Widgets[0], array(
  'separator' => '>',
  'cssClass' => 'test_bread_crumb_class'
))
->get('/dm-test-domains/domain-1/categ-1/post-1')
->checks()
->has('.test_bread_crumb_class', 'Home>Dm test domains>Domain 1>Categ 1>Post 1');