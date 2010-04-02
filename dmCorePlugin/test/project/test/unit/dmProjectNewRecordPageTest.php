<?php

require_once(realpath(dirname(__FILE__).'/../../..').'/unit/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot('front');

$t = new lime_test();

if(strpos(getcwd(), 'hudson'))
{
  return;
}

$helper->get('page_tree_watcher')->connect();

$nbPage = dmDb::table('DmPage')->count();

$domain = dmDb::table('DmTestDomain')->create(array(
  'title' => dmString::random(),
  'is_active' => false
))->saveGet();

$t->ok($domain->exists(), 'Record has been saved');

$t->ok(!$domain->isActive, 'Record is not active');

$t->is(dmDb::table('DmPage')->count(), $nbPage, 'No new page');

/*
 * With some old version of sqlite, like on continuous integration server
 * This test will not work as expected
 */
if(strpos(getcwd(), 'hudson'))
{
  return;
}

$t->ok($page = $domain->getDmPage(), 'Domain has a page');

$t->is(dmDb::table('DmPage')->count(), $nbPage+1, 'One new page');

$t->is($page->isActive, false, 'The page is not active');

$t->is($page->Record, $domain, 'The page record is the domain');

$domain = dmDb::table('DmTestDomain')->create(array(
  'title' => dmString::random(),
  'is_active' => true
))->saveGet();

$t->ok($domain->exists(), 'Record has been saved');

$t->ok($domain->isActive, 'Record is active');

$t->is(dmDb::table('DmPage')->count(), $nbPage+1, 'No new page');

$t->ok($page = $domain->getDmPage(), 'Domain has a page');

$t->is(dmDb::table('DmPage')->count(), $nbPage+2, 'One new page');

$t->is($page->isActive, true, 'The page is active');

$t->is($page->Record, $domain, 'The page record is the domain');

$categ = dmDb::table('DmTestCateg')->create(array(
  'name' => dmString::random(),
  'is_active' => false
))->saveGet();

$t->is(dmDb::table('DmPage')->count(), $nbPage+2, 'No new page');

$t->ok(!$categ->getDmPage(), 'Categ has a NO page');

$t->is(dmDb::table('DmPage')->count(), $nbPage+2, 'No new page');

$t->comment('link the categ to a domain');
$categ->Domains->add($domain);
$categ->save();

$t->is(dmDb::table('DmPage')->count(), $nbPage+2, 'No new page');

$t->ok($page = $categ->getDmPage(), 'Categ has a page');

$t->is(dmDb::table('DmPage')->count(), $nbPage+3, 'One new page');

$t->is($page->isActive, false, 'The page is not active');

$t->is($page->Record, $categ, 'The page record is the categ');