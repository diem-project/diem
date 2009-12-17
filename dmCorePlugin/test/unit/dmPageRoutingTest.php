<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot('front');

sfConfig::set('sf_cache', true);

$t = new lime_test(20);

// check base pages exist
dmDb::table('DmPage')->checkBasicPages();

// get services
$user     = $helper->get('user');
$i18n     = $helper->get('i18n');
$routing  = $helper->get('page_routing');

// add c1 and c2 cultures
$i18n->setCultures(array_merge($i18n->getCultures(), array('c1', 'c2')));

// reset user culture
$user->setCulture(sfConfig::get('sf_default_culture'));

// create test page
$pageModule = $pageAction = $pageName = $pageSlug = dmString::random();
$page = dmDb::create('DmPage', array(
  'module' => $pageModule,
  'action' => $pageModule,
  'name'   => $pageName,
  'slug'   => $pageSlug
));
$page->Node->insertAsLastChildOf(dmDb::table('DmPage')->getTree()->fetchRoot());

$c1PageSlug = 'c1/'.$pageSlug;
$c2PageSlug = 'c2/'.$pageSlug;
$page->Translation['c1']->name = $c1PageSlug;
$page->Translation['c1']->slug = $c1PageSlug;
$page->Translation['c2']->name = $c2PageSlug;
$page->Translation['c2']->slug = $c2PageSlug;

$page->save();

$t->ok($page->hasCurrentTranslation(), 'page has a translation');

$t->is($page->slug, $pageSlug, 'page slug is '.$pageSlug);

$t->diag('Find route for '.$pageSlug);
$route = $routing->find($pageSlug);

$t->isa_ok($route, 'dmPageRoute', 'found a dmPageRoute instance for '.$pageSlug);
$t->is($route->getSlug(), $pageSlug, 'route slug is '.$pageSlug);
$t->is($route->getPage(), $page, 'route page is the good page');
$t->is($route->getCulture(), $user->getCulture(), 'route culture is user culture : '.$route->getCulture());

$nonExistingSlug = dmString::random();
$t->diag('Find route for non existing slug '.$nonExistingSlug);
$route = $routing->find($nonExistingSlug);

$t->is($route, false, 'found no route for non existing slug '.$nonExistingSlug);

$t->diag('Switch user to c1 culture');
$user->setCulture('c1');

$t->is($helper->get('service_container')->getParameter('user.culture'), $user->getCulture(), 'service container user.culture synchronized');

$route = $routing->find($pageSlug);

$t->isa_ok($route, 'dmPageRoute', 'found a dmPageRoute instance for '.$pageSlug);
$t->is($route->getSlug(), $pageSlug, 'route slug is '.$pageSlug);
$t->is($route->getPage(), $page, 'route page is the good page');
$t->is($route->getCulture(), sfConfig::get('sf_default_culture'), 'route culture is default culture : '.$route->getCulture());

$route = $routing->find($c1PageSlug);

$t->isa_ok($route, 'dmPageRoute', 'found a dmPageRoute instance for '.$c1PageSlug);
$t->is($route->getSlug(), $c1PageSlug, 'route slug is '.$c1PageSlug);
$t->is($route->getPage(), $page, 'route page is the good page');
$t->is($route->getCulture(), $user->getCulture(), 'route culture is user culture : '.$route->getCulture());

$route = $routing->find($c2PageSlug);

$t->isa_ok($route, 'dmPageRoute', 'found a dmPageRoute instance for '.$c2PageSlug);
$t->is($route->getSlug(), $c2PageSlug, 'route slug is '.$c2PageSlug);
$t->is($route->getPage(), $page, 'route page is the good page');
$t->is($route->getCulture(), 'c2', 'route culture is c2 : '.$route->getCulture());

//clear the mess
$page->Node->delete();