<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test();

$table = dmDb::table('DmPage');

$page1 = $table->create(array(
  'name' => 'name1',
  'slug' => 'slug1',
  'module' => 'test',
  'action' => 'test1'
))->saveGet();

$t->ok($page1->exists(), 'Created a page');

$t->is('slug1', $page1->slug, 'Page slug is slug1');

$t->comment('Saving page with existing slug');

$page2 = $table->create(array(
  'name' => 'name2',
  'slug' => 'slug1',
  'module' => 'test',
  'action' => 'test2'
))->saveGet();

$t->ok($page2->exists(), 'Created a page');

$t->is($page2->slug, 'slug1-1', 'Page slug is slug1-1');

$page1->slug = 'slug-1';
$page1->save();

$t->is($page1->slug, 'slug-2', 'Page1 slug is now slug-2');

$page2->slug = 'slug';
$page2->save();

$t->is($page1->slug, 'slug', 'Page2 slug is now slug1');