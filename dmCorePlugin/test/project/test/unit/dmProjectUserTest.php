<?php

require_once(realpath(dirname(__FILE__).'/../../..').'/unit/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot('admin');

$t = new lime_test(7);

$user = $helper->get('user');

$guardUser = dmDb::table('DmUser')->findOneByUsername('writer');

$t->comment('Signin as writer');
$user->signIn($guardUser);

$t->ok($user->can('admin'), 'user can admin');

$t->ok($user->can('content'), 'useer can content');

$t->ok(!$user->can('system'), 'user can not system');

$t->ok(!$user->can('zone_add'), 'user can not zone_add');

$t->ok($user->can('admin, content'), 'user can admin, content');

$t->ok($user->can('admin, system'), 'user can not admin, system');

$t->ok(!$user->can('system, zone_add'), 'user can not system, zone_add');