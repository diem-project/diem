<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot('admin');

$t = new lime_test(18);

$t->comment('Weird version numbers will be shown during this test.');
$t->comment('Diem real version number is '.DIEM_VERSION);

class dmDiemVersionCheckMock extends dmDiemVersionCheck
{
  protected function initialize(array $options)
  {
    parent::initialize($options);

    $this->cache = array(
      '5.0' => '5.0.4',
      '5.1' => '5.1.0-BETA2'
    );
  }
}

$versionCheck = $helper->get('diem_version_check', 'dmDiemVersionCheckMock');

dm::setVersion('5.0.0-BETA1');

$t->is(dm::getVersion(), '5.0.0-BETA1', 'Diem version is 5.0.0-BETA1');

$t->ok($versionCheck->shouldCheck(), 'versionCheck should check');

$t->is($versionCheck->getLatestServerVersionForBranch('5.0'), '5.0.4', 'Latest version for 5.0 is 5.0.4');

$t->is($versionCheck->getLatestServerVersionForBranch('5.1'), '5.1.0-BETA2', 'Latest version for 5.1 is 5.1.0-BETA2');

$t->is($versionCheck->isUpToDate(), false, 'Diem is not up to date');

$t->is($versionCheck->getRecommendedUpgrade(), '5.0.4', 'Recommended upgrade is 5.0.4');

dm::setVersion('5.0.4');

$t->is(dm::getVersion(), '5.0.4', 'Diem version is 5.0.4');

$t->ok(!$versionCheck->shouldCheck(), 'versionCheck should not check');

$t->is($versionCheck->getLatestServerVersionForBranch('5.0'), '5.0.4', 'Latest version for 5.0 is 5.0.4');

$t->is($versionCheck->getLatestServerVersionForBranch('5.1'), '5.1.0-BETA2', 'Latest version for 5.1 is 5.1.0-BETA2');

$t->is($versionCheck->isUpToDate(), true, 'Diem is up to date');

$t->is($versionCheck->getRecommendedUpgrade(), null, 'Recommended upgrade is null');

dm::setVersion('5.1.0-ALPHA4');

$t->is(dm::getVersion(), '5.1.0-ALPHA4', 'Diem version is 5.1.0-ALPHA4');

$t->ok(!$versionCheck->shouldCheck(), 'versionCheck should not check');

$t->is($versionCheck->getLatestServerVersionForBranch('5.0'), '5.0.4', 'Latest version for 5.0 is 5.0.4');

$t->is($versionCheck->getLatestServerVersionForBranch('5.1'), '5.1.0-BETA2', 'Latest version for 5.1 is 5.1.0-BETA2');

$t->is($versionCheck->isUpToDate(), false, 'Diem is not up to date');

$t->is($versionCheck->getRecommendedUpgrade(), '5.1.0-BETA2', 'Recommended upgrade is 5.1.0-BETA2');

dm::setVersion(DIEM_VERSION);