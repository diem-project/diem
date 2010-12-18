<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test(24);

$t->diag('default culture tests');

$user = $helper->get('user');

$userCulture = $user->getCulture();

$t->is(myDoctrineRecord::getDefaultCulture(), $user->getCulture(), 'default culture is '.$user->getCulture());

$user->setCulture('en');

$t->is(myDoctrineRecord::getDefaultCulture(), $user->getCulture(), 'default culture is '.$user->getCulture());

$user->setCulture('es');

$t->is(myDoctrineRecord::getDefaultCulture(), $user->getCulture(), 'default culture is '.$user->getCulture());

$user->setCulture($userCulture);

$t->is(myDoctrineRecord::getDefaultCulture(), $user->getCulture(), 'default culture is '.$user->getCulture());

$t->diag('Basic record tests');

$layout = dmDb::create('DmLayout', array(
  'name' => dmString::random(),
  'css_class' => 'the_class'
))->saveGet();

  $t->ok($layout instanceof DmLayout, 'call saveGet() returns the record');

  $t->is(dmDb::create('DmLayout')->orNull(), null, 'call orNull() on new record returns null');

	$t->is($layout->orNull(), $layout, 'call orNull() on existing record returns the record');

$t->diag('Property access tests');

$t->is($layout->get('css_class'), 'the_class', '->get("css_class")');
$t->is($layout->getCssClass(), 'the_class', '->getCssClass()');
$t->is($layout->css_class, 'the_class', '->css_class');
$t->is($layout->cssClass, 'the_class', '->cssClass');
$t->is($layout['cssClass'], 'the_class', '["css_class"]');

$t->ok($layout->set('css_class', 'other_class'), '->set("css_class")');
$t->ok($layout->setCssClass('other_class'), '->setCssClass()');
$t->ok($layout->css_class = 'other_class', '->css_class');
$t->ok($layout->cssClass = 'other_class', '->cssClass');

$layout->delete();

dmDb::table('DmPage')->checkBasicPages();

$page = dmDb::table('DmPage')->findOne();
$page->set('auto_mod', 'test');

$t->is($page->auto_mod, 'test', '$page->get("auto_mod")');
$t->is($page->auto_mod, $page->get('auto_mod'), '$page->auto_mod');
$t->is($page->getAutoMod(), $page->get('auto_mod'), '$page->getAutoMod()');
$t->is($page->autoMod, $page->get('auto_mod'), '$page->autoMod');

$page->autoMod = 'changed';
$t->is($page->autoMod, 'changed', '$page->autoMod changed');

try
{
  $page->testWeirdAttribute;
  $t->fail('page->testWeirdAttribute does not exist');
}
catch(Doctrine_Record_UnknownPropertyException $e)
{
  $t->pass('page->testWeirdAttribute does not exist');
}

try
{
  $page->testWeirdMethod();
  $t->fail('page->testWeirdMethod() does not exist');
}
catch(Doctrine_Record_UnknownPropertyException $e)
{
  $t->pass('page->testWeirdMethod() does not exist');
}

try
{
  $page->getTestWeirdMethod();
  $t->fail('page->getTestWeirdMethod() does not exist');
}
catch(Doctrine_Record_UnknownPropertyException $e)
{
  $t->pass('page->getTestWeirdMethod() does not exist');
}