<?php

require_once(realpath(dirname(__FILE__).'/../../..').'/unit/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test(12);

$helper->clearDatabase($t);

$loremizer = $helper->get('table_loremizer');

$t->is(dmDb::table('DmTestPost')->count(), 0, 'No post exist');

$t->comment('Loremize post table');
$loremizer->execute(dmDb::table('DmTestPost'), 10);

$t->is(dmDb::table('DmTestPost')->count(), 10, '10 posts were created');

$t->is(dmDb::table('DmTestComment')->count(), 0, '0 comment exist');

$t->is(dmDb::table('DmTestCateg')->count(), 1, '1 categ exist');

$t->is(dmDb::table('DmTestDomain')->count(), 0, '0 domain exist');

$t->comment('Loremize categ table');
$loremizer->execute(dmDb::table('DmTestCateg'), 10);

$t->is(dmDb::table('DmTestCateg')->count(), 10, '10 categ exist');

$t->is(dmDb::table('DmTestDomain')->count(), 0, '0 domain exist');

$t->is(dmDb::table('DmTestComment')->count(), 0, '0 comment exist');

$t->comment('Loremize post table');
$loremizer->execute(dmDb::table('DmTestPost'), 30);

$t->is(dmDb::table('DmTestPost')->count(), 30, '30 posts were created');

$t->comment('Loremize domain table');
$loremizer->execute(dmDb::table('DmTestDomain'), 30);

$t->is(dmDb::table('DmTestDomain')->count(), 30, '30 domains were created');

$t->comment('Loremize comment table');
$loremizer->execute(dmDb::table('DmTestComment'), 30);

$t->is(dmDb::table('DmTestComment')->count(), 30, '30 comments were created');

$t->comment('Loremize fruit table');
$loremizer->execute(dmDb::table('DmTestFruit'), 30);

$t->is(dmDb::table('DmTestFruit')->count(), 30, '30 fruits were created');