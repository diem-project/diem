<?php

require_once(realpath(dirname(__FILE__).'/../../..').'/unit/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test();

$user = dmDb::query('DmUser u')->where('u.username = ?', 'admin')->fetchOne();
$t->comment('Signin '.$user);
$helper->get('user')->signin($user);

$domain = dmDb::table('DmTestDomain')->create(array('title' => dmString::random()))->saveGet();

$t->is((string)$domain->getCurrentTranslation()->CreatedBy, 'admin');

$t->is((string)$domain->CreatedBy, 'admin');