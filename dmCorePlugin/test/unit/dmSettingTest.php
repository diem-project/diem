<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test(13);

$t->ok(count(dmConfig::getAll()) > 0, 'dmConfig is not empty');

$user = $helper->get('user');

$userCulture = $user->getCulture();

$t->is(dmConfig::getCulture(), $user->getCulture(), 'dmConfig culture is '.$user->getCulture());

$user->setCulture('en');

$t->is(dmConfig::getCulture(), $user->getCulture(), 'dmConfig culture is '.$user->getCulture());

$user->setCulture('es');

$t->is(dmConfig::getCulture(), $user->getCulture(), 'dmConfig culture is '.$user->getCulture());

$user->setCulture($userCulture);

$t->is(dmConfig::getCulture(), $user->getCulture(), 'dmConfig culture is '.$user->getCulture());

$defaultCulture = sfConfig::get('sf_default_culture');
$user->setCulture($defaultCulture);

dmDb::query('DmSetting s')->where('s.name = ?', 'test')->delete()->execute();
$t->diag('create new setting');
$setting = dmDb::create('DmSetting', array(
  'name' => 'test',
  'description' => 'This is just a test setting',
  'value' => $defaultCulture.' value',
  'default_value' => $defaultCulture.' default',
  'type' => 'text',
  'group_name' => 'test group'
))->saveGet();

dmConfig::load();

try
{
  dmConfig::get('i_do_not_exist_for_sure');
  $t->pass('Get non-existant config');
}
catch(dmException $e)
{
	$t->fail('Get non-existant config');
}

try
{
  dmConfig::set('i_do_not_exist_for_sure', 'value');
  $t->pass('Set non-existant config');
}
catch(dmException $e)
{
	$t->fail('Set non-existant config');
}

$t->is(dmConfig::get('test'), $defaultCulture.' value', 'test value is '.$defaultCulture.' value');

dmConfig::set('test', 'new '.$defaultCulture.' value');

$t->is(dmConfig::get('test'), 'new '.$defaultCulture.' value', 'test value is new '.$defaultCulture.' value');

dmConfig::load();

$t->is(dmConfig::get('test'), 'new '.$defaultCulture.' value', 'test value is new '.$defaultCulture.' value');

$user->setCulture('c1');

$t->is(dmConfig::get('test'), 'new '.$defaultCulture.' value', 'test value is new '.$defaultCulture.' value ( by non-existing c1 culture fallback )');

dmConfig::set('test', 'c1 value');

$t->is(dmConfig::get('test'), 'c1 value', 'test value is c1 value');

$user->setCulture($defaultCulture);

$t->is(dmConfig::get('test'), 'new '.$defaultCulture.' value', 'test value is new '.$defaultCulture.' value');

$user->setCulture($userCulture);

$setting->delete();