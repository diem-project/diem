<?php

require_once(realpath(dirname(__FILE__).'/../../..').'/unit/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot('front');

$t = new lime_test();

$page1 = dmDb::table('DmPage')->findOneByModuleAndAction('main', 'page1');
$page11 = dmDb::table('DmPage')->findOneByModuleAndAction('main', 'page11');

$page11->isActive = true;
$page11->save();

dm::loadHelpers(array('Dm'));

$t->like($link = £link($page11)->render(), '|<a class="link" href=".+">Page 11</a>|', 'Link to active page: '.$link);

$page11->isActive = false;
$page11->save();

$t->is($link = £link($page11)->render(), '<span class="link">Page 11</span>', 'Link to inactive page: '.$link);