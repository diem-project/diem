<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test(12);

$t->comment('Weird version numbers will be shown during this test.');
$t->comment('Diem real version number is '.DIEM_VERSION);

dm::setVersion('5.2.14');

$t->is(dm::getVersion(), '5.2.14', 'Diem version is 5.2.14');

$t->is(dm::getVersionMajor(), '5', 'Diem version major is 5');

$t->is(dm::getVersionMinor(), '2', 'Diem version minor is 2');

$t->is(dm::getVersionMaintenance(), '14', 'Diem version maintenance is 14');

$t->is(dm::getVersionBranch(), '5.2', 'Diem version branch is 5.2');

dm::setVersion('5.0.0-BETA4_DEV');

$t->is(dm::getVersion(), '5.0.0-BETA4_DEV', 'Diem version is 5.0.0-BETA4_DEV');

$t->is(dm::getVersionMajor(), '5', 'Diem version major is 5');

$t->is(dm::getVersionMinor(), '0', 'Diem version minor is 0');

$t->is(dm::getVersionMaintenance(), '0-BETA4_DEV', 'Diem version maintenance is 0-BETA4_DEV');

$t->is(dm::getVersionBranch(), '5.0', 'Diem version branch is 5.0');

dm::setVersion(DIEM_VERSION);

$t->is(version_compare('5.0.0-BETA4_DEV', '5.0.0'), -1, '5.0.0-BETA4_DEV < 5.0.0');

$t->is(version_compare('5.0.0', '5.0.1'), -1, '5.0.0 < 5.0.1');