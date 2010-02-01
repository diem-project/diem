<?php

require_once(realpath(dirname(__FILE__).'/../../..').'/unit/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot('front');

$t = new lime_test(18);

$page1    = dmDb::table('DmPage')->findOneByModuleAndAction('main', 'page1');
$page11   = dmDb::table('DmPage')->findOneByModuleAndAction('main', 'page11');
$page12   = dmDb::table('DmPage')->findOneByModuleAndAction('main', 'page12');

$absoluteUrlRoot = $helper->get('helper')->link('@homepage')->getHref().'/';

$menu = $helper->get('menu')->name('Test Menu');

$page1Child = $menu->addChild('page1', 'page:'.$page1->id);

$t->is($page1Child->getNbChildren(), 0, 'page1Child has no children');

$t->is($menu->render(), $expected = '<ul><li class="first last"><a class="link" href="'.$absoluteUrlRoot.'page1">page1</a></li></ul>', $expected);

$t->comment('Recursive from external link');

$externalChild = $menu->addChild('external', 'http://jquery.com');
try
{
  $externalChild->addRecursiveChildren(1);
  $t->pass('Recursive from external link does not throw an exception');
}
catch(dmException $e)
{
  $t->fail('Recursive from external link does not throw an exception');
}

$t->is($externalChild->getNbChildren(), 0, 'externalChild has no child');

$menu->removeChild($externalChild);

$page1Child->addRecursiveChildren(0);

$t->is($page1Child->getNbChildren(), 0, 'page1Child has no child');

$t->comment('Add recursive children with depth 1 to page1Child');
$page1Child->addRecursiveChildren(1);

$t->is($page1Child->getNbChildren(), 2, 'page1Child has 2 child');

$t->is($page1Child['Page 11']->renderChild(), $expected = '<li class="first"><a class="link" href="'.$absoluteUrlRoot.'page11">Page 11</a></li>', 'page1Child[Page 11] = '.$expected);

$t->is($page1Child['Page 12']->renderChild(), $expected = '<li class="last"><a class="link" href="'.$absoluteUrlRoot.'page12">Page 12</a></li>', 'page1Child[Page 12] = '.$expected);

$t->is($menu->render(), $expected = '<ul><li class="first last"><a class="link" href="'.$absoluteUrlRoot.'page1">page1</a><ul><li class="first"><a class="link" href="'.$absoluteUrlRoot.'page11">Page 11</a></li><li class="last"><a class="link" href="'.$absoluteUrlRoot.'page12">Page 12</a></li></ul></li></ul>', $expected);

$t->comment('Again: Add recursive children with depth 1 to page1Child');
$page1Child->addRecursiveChildren(1);

$t->is($page1Child->getNbChildren(), 2, 'page1Child has 2 child');

$t->is($menu->render(), $expected = '<ul><li class="first last"><a class="link" href="'.$absoluteUrlRoot.'page1">page1</a><ul><li class="first"><a class="link" href="'.$absoluteUrlRoot.'page11">Page 11</a></li><li class="last"><a class="link" href="'.$absoluteUrlRoot.'page12">Page 12</a></li></ul></li></ul>', $expected);

$t->comment('Remove page1Child children');
$page1Child->removeChildren();

$t->is($page1Child->getNbChildren(), 0, 'page1Child has no child');

$t->comment('Again: Add recursive children with depth 2 to page1Child');
$page1Child->addRecursiveChildren(2);

$t->is($page1Child->getNbChildren(), 2, 'page1Child has 2 child');

$t->is($page1Child['Page 11']->getNbChildren(), 1, '$page1Child["Page 11"] has 2 child');

$t->is($menu->render(), $expected = '<ul><li class="first last"><a class="link" href="'.$absoluteUrlRoot.'page1">page1</a><ul><li class="first"><a class="link" href="'.$absoluteUrlRoot.'page11">Page 11</a><ul><li class="first last"><a class="link" href="'.$absoluteUrlRoot.'page111">Page 111</a></li></ul></li><li class="last"><a class="link" href="'.$absoluteUrlRoot.'page12">Page 12</a></li></ul></li></ul>', $expected);

$t->comment('Again: Add recursive children with depth 3 to page1Child');
$page1Child->addRecursiveChildren(3);

$t->is($page1Child->getNbChildren(), 2, 'page1Child has 2 child');

$t->is($page1Child['Page 11']->getNbChildren(), 1, '$page1Child["Page 11"] has 2 child');

$t->is($menu->render(), $expected = '<ul><li class="first last"><a class="link" href="'.$absoluteUrlRoot.'page1">page1</a><ul><li class="first"><a class="link" href="'.$absoluteUrlRoot.'page11">Page 11</a><ul><li class="first last"><a class="link" href="'.$absoluteUrlRoot.'page111">Page 111</a></li></ul></li><li class="last"><a class="link" href="'.$absoluteUrlRoot.'page12">Page 12</a></li></ul></li></ul>', $expected);