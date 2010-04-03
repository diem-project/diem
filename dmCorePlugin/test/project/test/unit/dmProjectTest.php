<?php

require_once(realpath(dirname(__FILE__).'/../../..').'/unit/helper/dmModuleUnitTestHelper.php');
$helper = new dmModuleUnitTestHelper();
$helper->boot();

$t = new lime_test(10);

$models = dmProject::getModels();
$dmModels = dmProject::getDmModels();
$allModels = dmProject::getAllModels();

$t->is_deeply($models, $expected = array(
  'DmTestCateg', 'DmTestComment', 'DmTestDomainCateg', 'DmTestDomain', 'DmTestFruit', 'DmTestPost', 'DmTestPostTag', 'DmTestTag', 'DmTestUser', 'DmContact', 'DmTag'
), 'dmProject::getModels() -> '.implode(', ', $expected));

$t->is_deeply(array_intersect($models, $allModels), $models, 'dmProject::getAllModels() contain all project models');

$t->is_deeply(array_intersect($dmModels, $allModels), $dmModels, 'dmProject::getAllModels() contain all Diem models');

$t->is_deeply(array_intersect($models, $dmModels), array(), 'dmProject::getDmModels() contain no project models');

$t->is_deeply(array_intersect($dmModels, $models), array(), 'dmProject::getModels() contain no Diem models');

$t->is(count($dmModels), $expected = 17, 'Diem has '.$expected.' models');

$t->is(dmProject::getKey(), 'project', 'project key is "project"');

$t->ok(dmProject::appExists('front'), 'project has a front app');

$t->ok(dmProject::appExists('admin'), 'project has a admin app');

$t->ok(!dmProject::appExists('bluk'), 'project has no bluk app');