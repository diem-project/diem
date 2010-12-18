<?php

require_once(realpath(dirname(__FILE__).'/../../..').'/unit/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot('front');

$pager = $helper->get('service_container')
->setParameter('doctrine_pager.model', 'DmTestPost')
->getService('doctrine_pager')
->setMaxPerPage(5)
->setQuery($query = dmDb::query('DmTestPost t'))
->setPage(1)
->init();

$t = new lime_test(16);

$t->isa_ok($pager, 'dmDoctrinePager', 'Got a dmDoctrinePager instance');

$t->is($pager->getNbResults(), 20, 'Pager has 20 results');

$pager = $helper->get('front_pager_view')
->setPager($pager)
->setOption('navigation_top', true)
->setOption('navigation_bottom', true);

$t->isa_ok($pager, 'dmFrontPagerView', 'Got a dmFrontPagerView instance');

$t->is($pager->getNbResults(), 20, 'Pager has 20 results');

// Countable interface
$t->diag('Countable interface');

$t->is(count($pager), 20, 'Pager has 20 results');

// Iterator interface
$t->diag('Iterator interface');

$pager->init();
$normal = 0;
$iterated = 0;
foreach ($pager->getResults() as $object)
{
  $normal++;
}
foreach ($pager as $object)
{
  $iterated++;
}
$t->is($iterated, $normal, '"Iterator" interface loops over objects in the current pager');

$t->is($pager->getCurrent(), $query->fetchOne(), 'Found the first post');

$t->comment('Render navigation top');
$pattern = '<div class="pager"><ul class="clearfix"><li class="page current"><span class="link">1</span></li><li class="page">.+</li></ul></div>';
$t->like($navigation = $pager->renderNavigationTop(), '|^'.$pattern.'$|', 'navigation top: '.$navigation);

$t->comment('Pass custom classes with array');
$pager->setOption('class', 'custom_class1 class2');
$pattern = '<div class="pager custom_class1 class2"><ul class="clearfix"><li class="page current"><span class="link">1</span></li><li class="page">.+</li></ul></div>';
$t->like($navigation = $pager->renderNavigationTop(), '|^'.$pattern.'$|', 'navigation top: '.$navigation);

$t->comment('Pass custom classes with CSS expression');
$pager->setOption('class', null);
$pattern = '<div class="pager custom_class1 class2"><ul class="clearfix"><li class="page current"><span class="link">1</span></li><li class="page">.+</li></ul></div>';
$t->like($navigation = $pager->renderNavigationTop('.custom_class1.class2'), '|^'.$pattern.'$|', 'navigation top: '.$navigation);

$t->comment('Disable navigation top');
$pager->setOption('class', null)->setOption('navigation_top', false);
$t->is($navigation = $pager->renderNavigationTop(), '', 'navigation top: '.$navigation);

$t->comment('Pass custom separator');
$pager->setOption('navigation_top', true)->setOption('separator', '-');
$pattern = '<div class="pager"><ul class="clearfix"><li class="page current"><span class="link">1</span></li><li class="separator">-</li><li class="page">.+</li></ul></div>';
$t->like($navigation = $pager->renderNavigationTop(), '|^'.$pattern.'$|', 'navigation top: '.$navigation);

$t->comment('Pass custom current class');
$pager->setOption('separator', false)->setOption('current_class', 'my_current');
$pattern = '<div class="pager"><ul class="clearfix"><li class="page my_current"><span class="link">1</span></li><li class="page">.+</li></ul></div>';
$t->like($navigation = $pager->renderNavigationTop(), '|^'.$pattern.'$|', 'navigation top: '.$navigation);

$t->comment('Change page to 2');
$pager->setOption('current_class', 'current')->setPage(2);
$pattern = '<div class="pager"><ul class="clearfix"><li class="page first">.+</li><li class="page prev">.+</li><li class="page">.+</li></ul></div>';
$t->like($navigation = $pager->renderNavigationTop(), '|^'.$pattern.'$|', 'navigation top: '.$navigation);

$t->comment('With 100 records per page');
$pager->setPage(1)->setMaxPerPage(100);
$t->is($navigation = $pager->renderNavigationTop(), '', 'navigation top: '.$navigation);

$t->comment('With nb_links = 0');
$pager->setPage(1)->setMaxPerPage(5)->setOption('nb_links', 0);
$pattern = '<div class="pager"><ul class="clearfix"><li class="page next">.+</li></ul></div>';
$t->like($navigation = $pager->renderNavigationTop(), '|^'.$pattern.'$|', 'navigation top: '.$navigation);