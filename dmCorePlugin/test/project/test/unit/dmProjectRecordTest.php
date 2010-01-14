<?php

require_once(realpath(dirname(__FILE__).'/../../..').'/unit/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test(120);

$helper->loremizeDatabase(15, $t);

$fruit = dmDb::table('DmTestFruit')->findOne();
$domain = dmDb::table('DmTestDomain')->findOne();
$categ = dmDb::table('DmTestCateg')->findOne();
$post = dmDb::table('DmTestPost')->findOne();
$comment = dmDb::table('DmTestComment')->findOne();
$tag = dmDb::table('DmTestTag')->findOne();

$t->comment('Add more associations');
if (!$categ->Domains->count())
{
  $categ->Domains[] = dmDb::table('DmTestDomain')->findOne();
  $categ->save();
  $domain->refreshRelated('Categs');
}
if (!$tag->Posts->count())
{
  dmDb::table('DmTestPostTag')->create(array(
    'post_id' => $post->id,
    'tag_id' => $tag->id
  ))->save();
  $tag->refreshRelated('Posts');
  $post->refreshRelated('Tags');
}

$t->diag('Related record tests with record hydration');

  try { $fruit->getRelatedRecord('DmTestPost'); $ok = false; }
  catch(dmRecordException $e) { $ok = true; }
  $t->ok($ok, 'Fruit has no related DmTestPost');

  try { $fruit->getRelatedRecord('Fruit'); $ok = false; }
  catch(dmRecordException $e) { $ok = true; }
  $t->ok($ok, 'Fruit has no related Fruit');

  try { $post->getRelatedRecord('DmTestDomain'); $ok = false; }
  catch(dmRecordException $e) { $ok = true; }
  $t->ok($ok, 'DmTestPost has no related DmTestDomain');

  $t->ok($categ->getRelatedRecord('DmTestDomain') instanceof DmTestDomain, 'DmTestCateg has related DmTestDomain');

  $t->ok($post->getRelatedRecord('DmTestCateg') instanceof DmTestCateg, 'DmTestPost has related DmTestCateg');

  $t->ok($comment->getRelatedRecord('DmTestPost') instanceof DmTestPost, 'DmTestComment has related DmTestPost');

  $t->ok($tag->getRelatedRecord('DmTestPost') instanceof DmTestPost, 'DmTestTag has related DmTestPost');

  $t->ok($post->getRelatedRecord('DmTestTag') instanceof DmTestTag, 'DmTestPost has related DmTestTag');

$t->diag('Related record tests with array hydration');
$hydrationMode = Doctrine::HYDRATE_ARRAY;

  try { $fruit->getRelatedRecord('DmTestPost', $hydrationMode); $ok = false; }
  catch(dmRecordException $e) { $ok = true; }
  $t->ok($ok, 'Fruit has no related DmTestPost');

  try { $fruit->getRelatedRecord('Fruit', $hydrationMode); $ok = false; }
  catch(dmRecordException $e) { $ok = true; }
  $t->ok($ok, 'Fruit has no related Fruit');

  try { $post->getRelatedRecord('DmTestDomain', $hydrationMode); $ok = false; }
  catch(dmRecordException $e) { $ok = true; }
  $t->ok($ok, 'DmTestPost has no related DmTestDomain');

$tested = $categ->getRelatedRecord('DmTestDomain', $hydrationMode);
  $t->ok(is_array($tested) && $tested['id'] == $categ->getRelatedRecord('DmTestDomain')->id, 'DmTestCateg has related DmTestDomain');

$tested = $post->getRelatedRecord('DmTestCateg', $hydrationMode);
  $t->ok(is_array($tested) && $tested['id'] == $post->getRelatedRecord('DmTestCateg')->id, 'DmTestPost has related DmTestCateg');

$tested = $tag->getRelatedRecord('DmTestPost', $hydrationMode);
  $t->ok(is_array($tested) && $tested['id'] == $tag->getRelatedRecord('DmTestPost')->id, 'DmTestTag has related DmTestPost');

$tested = $post->getRelatedRecord('DmTestTag', $hydrationMode);
  $t->ok(is_array($tested) && $tested['id'] == $post->getRelatedRecord('DmTestTag')->id, 'DmTestPost has related DmTestTag');

$t->diag('Related record test : verifying that we get the first related record in local relation');

foreach(dmDb::table('DmTestPost')->findAll() as $po)
{
  $cat1 = $po->Categ->orNull();
  $cat2 = $po->getRelatedRecord('DmTestCateg');

  $t->is((string)$cat1, (string)$cat2,
    sprintf('post %d related categ : %s / %s',
    $po->id, $cat1 ? $cat1->id : 'NULL', $cat2 ? $cat2->id : 'NULL'
  ));
}

$t->diag('Related record test : verifying that we get the first related record in reversed local relation');

foreach(dmDb::table('DmTestCateg')->findAll() as $cat)
{
  $post1 = $cat->Posts[0]->orNull();
  $post2 = $cat->getRelatedRecord('DmTestPost');

  $t->is((string)$post1, (string)$post2,
    sprintf('categ %d related post : %s / %s',
    $cat->id, $post1 ? $post1->id : 'NULL', $post2 ? $post2->id : 'NULL'
  ));
}

$t->diag('Related record test : verifying that we get the first related record in association relation');

foreach(dmDb::table('DmTestCateg')->findAll() as $cat)
{
  $dom1 = $cat->Domains[0]->orNull();
  $dom2 = $cat->getRelatedRecord('DmTestDomain');

  $t->is((string)$dom1, (string)$dom2,
    sprintf('categ %d related domain : %s / %s',
    $cat->id, $dom1 ? $dom1->id : 'NULL', $dom2 ? $dom2->id : 'NULL'
  ));
  
  if ($dom1 != $dom2)
  {
    dmDebug::kill($cat->Domains, $cat->getRelatedRecord('DmTestDomain'));
  }
}

$t->diag('Related record test : verifying that we get the first related record in reverse association relation');

foreach(dmDb::table('DmTestDomain')->findAll() as $dom)
{
  $cat1 = $dom->Categs[0]->orNull();
  $cat2 = $dom->getRelatedRecord('DmTestCateg');

  $t->is((string)$cat1, (string)$cat2,
    sprintf('domain %d related categ : %s / %s',
    $dom->id, $cat1 ? $cat1->id : 'NULL', $cat2 ? $cat2->id : 'NULL'
  ));
}

$t->diag('Related record test : verifying that we get the first related record in another association relation');

foreach(dmDb::table('DmTestTag')->findAll() as $_tag)
{
  $post1 = $_tag->Posts[0]->orNull();
  $post2 = $_tag->getRelatedRecord('DmTestPost');

  $t->is((string)$post1, (string)$post2,
    sprintf('tag %d related post : %s / %s',
    $_tag->id, $post1 ? $post1->id : 'NULL', $post2 ? $post2->id : 'NULL'
  ));
}

//$t->diag('Related record id tests');
//
//  try { $fruit->getRelatedRecordId('DmTestPost'); $ok = false; }
//  catch(dmRecordException $e) { $ok = true; }
//  $t->ok($ok, 'Fruit has no related DmTestPost id');
//
//  try { $fruit->getRelatedRecordId('Fruit'); $ok = false; }
//  catch(dmRecordException $e) { $ok = true; }
//  $t->ok($ok, 'Fruit has no related Fruit id');
//
//  try { $post->getRelatedRecordId('DmTestDomain'); $ok = false; }
//  catch(dmRecordException $e) { $ok = true; }
//  $t->ok($ok, 'DmTestPost has no related DmTestDomain id');
//
//$domainId = $categ->getRelatedRecord('DmTestDomain')->id;
//  $t->is($categ->getRelatedRecordId('DmTestDomain'), $domainId, 'DmTestCateg has related DmTestDomain id');
//
//$categId = $post->getRelatedRecord('DmTestCateg')->id;
//  $t->is($post->getRelatedRecordId('DmTestCateg'), $categId, 'DmTestPost has related DmTestCateg id');
//
//$postId = $tag->getRelatedRecord('DmTestPost')->id;
//  $t->is($tag->getRelatedRecordId('DmTestPost'), $postId, 'DmTestTag has related DmTestPost id');
//
//$postId = $comment->getRelatedRecord('DmTestPost')->id;
//  $t->is($comment->getRelatedRecordId('DmTestPost'), $postId, 'DmTestComment has related DmTestPost id');

