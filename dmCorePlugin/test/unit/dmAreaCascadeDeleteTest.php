<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->bootFast();

$t = new lime_test();

$nbAreas = dmDb::table('DmArea')->count();

$t->comment($nbAreas.' areas');

$pageView = dmDb::table('DmPageView')->create(array(
  'module' => dmString::random(),
  'action' => dmString::random()
))->saveGet();

$area = $pageView->Area;

$t->ok($pageView->exists(), 'Created a pageView');
$t->ok($area->exists(), 'Created an area');
$t->is(dmDb::table('DmArea')->count(), $nbAreas+1, $nbAreas.'+1 in db');

$pageView->delete();

$t->ok(!$pageView->exists(), 'Deleted the pageView');
$t->ok(!$area->exists(), 'Deleted the area by cascade');
$t->is(dmDb::table('DmArea')->count(), $nbAreas, $nbAreas.' in db');

$layout = dmDb::table('DmLayout')->create(array(
  'name' => dmString::random()
))->saveGet();

$area = $layout->getArea('top');

$t->ok($layout->exists(), 'Created a layout');
$t->ok($area->exists(), 'Created a top area');
$t->is(dmDb::table('DmArea')->count(), $nbAreas+1, $nbAreas.'+1 in db');

$layout->delete();

$t->ok(!$layout->exists(), 'Deleted the layout');
$t->ok(!$area->exists(), 'Deleted the area by cascade');
$t->is(dmDb::table('DmArea')->count(), $nbAreas, $nbAreas.' in db');