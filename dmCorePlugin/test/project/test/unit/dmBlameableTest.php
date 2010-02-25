<?php

require_once(realpath(dirname(__FILE__).'/../../..').'/unit/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test();

$user = dmDb::query('DmUser u')->where('u.username = ?', 'admin')->fetchOne();
$newUser = dmDb::table('DmUser')->create(array(
  'username' => 'toto',
  'email' => 'zgezqhrzrth@gmmial.com'
))->saveGet();

$t->comment('Existing categ');
$fruit = dmDb::query('DmTestFruit')->orderBy('RANDOM()')->fetchOne();

$t->is($fruit->hasReference('CreatedBy'), false);
$t->is($fruit->hasReference('UpdatedBy'), false);

$t->comment('New categ');
$fruit = dmDb::table('DmTestFruit')->create(array('title' => dmString::random()));
$fruit->save();

$t->is($fruit->hasReference('CreatedBy'), false);
$t->is($fruit->hasReference('UpdatedBy'), false);

$t->comment('Signin '.$user);
$helper->get('user')->signin($user);

$fruit = dmDb::table('DmTestFruit')->create(array('title' => dmString::random()));
$fruit->save();

$fruit->refresh(true);

$t->is((string)$fruit->CreatedBy, 'admin');
$t->is((string)$fruit->UpdatedBy, 'admin');

$t->comment('Signin '.$newUser);
$helper->get('user')->signin($newUser);

$fruit->title = 'changed title';
$fruit->save();

$fruit->refresh(true);

$t->is((string)$fruit->CreatedBy, 'admin');
$t->is((string)$fruit->UpdatedBy, 'toto');

$t->comment('I18n blameable');

$helper->get('user')->signout();

$domain = dmDb::table('DmTestDomain')->create(array('title' => dmString::random()))->saveGet();

$t->is((string)$domain->CreatedBy, '');
$t->is((string)$domain->UpdatedBy, '');

$t->comment('Signin '.$user);
$helper->get('user')->signin($user);

$domain = dmDb::table('DmTestDomain')->create(array('title' => dmString::random()))->saveGet();

$t->is((string)$domain->CreatedBy, 'admin');
$t->is((string)$domain->UpdatedBy, 'admin');

$t->comment('Signin '.$newUser);
$helper->get('user')->signin($newUser);

$domain->title = 'changed title';
$domain->save();

$domain->getCurrentTranslation()->refresh(true);

$t->is((string)$domain->CreatedBy, 'admin');
$t->is((string)$domain->UpdatedBy, 'toto');