<?php

require_once(realpath(dirname(__FILE__).'/../../..').'/unit/helper/dmModuleUnitTestHelper.php');
$helper = new dmModuleUnitTestHelper();
$helper->boot();

$t = new lime_test();

$t->isa_ok(dmDb::table('DmTag'), 'DmTagTable', 'DmTagTable exists without loading any model');

$t->is(dmDb::table('DmTag')->count(), 20, 'DmTagTable has 20 tags');

$t->ok(dmDb::table('DmTestDomain')->hasTemplate('DmTaggable'), 'DmTestDomain is taggable');
$t->ok(dmDb::table('DmTestFruit')->hasTemplate('DmTaggable'), 'DmTestFruit is taggable');
$t->ok(!dmDb::table('DmTestPost')->hasTemplate('DmTaggable'), 'DmTestPost is not taggable');

$domain = dmDb::table('DmTestDomain')->findOne();
$domain->title = 'Domain';

$domain->removeAllTags()->save();

$t->is($domain->getNbTags(), 0, $domain.' has 0 tags');

$t->ok(!$domain->hasTags(), $domain.' has no tags');

$domain->setTags('tag1, tag2, tag3');

$t->is($domain->getNbTags(), 3, $domain.' has 3 tags');

$t->ok($domain->hasTags(), $domain.' has tags');

$domain->addTags('tag1, tag4, tag5');

$t->is($domain->getNbTags(), 5, $domain.' has 5 tags');

$tag1 = dmDb::table('DmTag')->findOneByName('tag1');

$t->isa_ok($tag1, 'DmTag', 'tag1 is a DmTag');

$t->ok($tag1->exists(), 'tag1 exists');

$t->is($domain->getTags()->getFirst(), $tag1, $domain.' first tag is tag1');

$t->is($domain->getTagNames(), $expected = array('tag1', 'tag2', 'tag3', 'tag4', 'tag5'), $domain.' tag names = '.implode(', ', $expected));

$t->is($domain->getTagsString(), $expected = 'tag1, tag2, tag3, tag4, tag5', $domain.' tags string = '.$expected);

$domain->removeTags('tag4, tag5');

$t->is($domain->getTagsString(), $expected = 'tag1, tag2, tag3', $domain.' tags string = '.$expected);

$domain->removeAllTags()->save();

$t->ok(!$domain->hasTags(), $domain.' has no more tags');

dmDb::query('DmTag t')->delete()->execute();

$t->is(dmDb::table('DmTag')->count(), 0, 'All tags were deleted');

$domain2 = dmDb::table('DmTestDomain')->create(array(
  'title' => 'Domain2'
))->saveGet();

$t->ok($domain->hasRelatedRecords(), $domain.' has no related records');
$t->ok($domain2->hasRelatedRecords(), $domain2.' has no related records');

$domain->addTags('tag1, tag2, tag3')->save();
$domain2->addTags('tag1, tag2, tag4, tag5')->save();

$t->ok($domain->hasRelatedRecords(), $domain.' has related records');
$t->ok($domain2->hasRelatedRecords(), $domain2.' has related records');

$t->is($domain->getRelatedRecords()->count(), 1, $domain.' has one related record');
$t->is($domain2->getRelatedRecords()->count(), 1, $domain2.' has one related record');

$t->is($domain->getRelatedRecords()->getFirst(), $domain2, $domain.' related record is '.$domain2);
$t->is($domain2->getRelatedRecords()->getFirst(), $domain, $domain2.' related record is '.$domain);

$fruit = dmDb::table('DmTestFruit')->findOne();
$fruit->title = 'Fruit';

$fruit->setTags('tag1, tag2, tag3, tag6')->save();

$t->is($fruit->getTagsString(), $expected = 'tag1, tag2, tag3, tag6', $fruit.' tags string = '.$expected);

$t->ok(!$fruit->hasRelatedRecords(), $fruit.' has no related records');

$popularTags = dmDb::table('DmTag')->getPopularTags();

$t->is(count($popularTags), 6, 'There are 6 popular tags');

$firstTag = $popularTags->getFirst();
$t->is_deeply($firstTag->toArray(), $expected = array(
  'id' => '26',
  'name' => 'tag1',
  'num_dm_test_domains' => '2',
  'num_dm_test_fruits' => '1',
  'total_num' => '3'
), 'Most popular tag is tag1');

$lastTag = $popularTags->getLast();
$t->is_deeply($lastTag->toArray(), $expected = array(
  'id' => '31',
  'name' => 'tag6',
  'num_dm_test_domains' => '0',
  'num_dm_test_fruits' => '1',
  'total_num' => '1'
), 'Less popular tag is tag6');