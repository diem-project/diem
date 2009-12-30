<?php

require_once(realpath(dirname(__FILE__).'/../../..').'/unit/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test(23);

$helper->loremizeDatabase(5, $t);

$fruit = dmDb::table('DmTestFruit')->findOne();
$domain = dmDb::table('DmTestDomain')->findOne();
$categ = dmDb::table('DmTestCateg')->findOne();
$post = dmDb::table('DmTestPost')->findOne();
$comment = dmDb::table('DmTestComment')->findOne();
$tag = dmDb::table('DmTestTag')->findOne();

$t->comment('Adding relations');

foreach(array($categ, $post->Categ, $comment->Post->Categ) as $_categ)
{
  if (!$_categ->Domains->count())
  {
    dmDb::table('DmTestDomainCateg')->create(array(
      'domain_id' => $domain->id,
      'categ_id' => $_categ->id
    ))->save();
    $_categ->refreshRelated('Domains');
    $domain->refreshRelated('Categs');
    
    $t->ok($_categ->Domains->count() && $_categ->Domains[0]->exists(), 'Now the categ has at least one domain');
  }
  else
  {
    $t->ok($_categ->Domains[0]->exists(), 'The categ already has at least one domain');
  }
}

$t->diag('Ancestor record tests');

  try { $fruit->getAncestorRecord('DmTestPost'); $ok = false; }
  catch(dmRecordException $e) { $ok = true; }
  $t->ok($ok, 'Fruit has no ancestor DmTestPost');

  try { $tag->getAncestorRecord('DmTestCateg'); $ok = false; }
  catch(dmRecordException $e) { $ok = true; }
  $t->ok($ok, 'DmTestTag has no ancestor DmTestCateg');

  try { $categ->getAncestorRecord('DmTestPost'); $ok = false; }
  catch(dmRecordException $e) { $ok = true; }
  $t->ok($ok, 'DmTestCateg has no ancestor DmTestPost');

  $t->isa_ok($categ->getAncestorRecord('DmTestDomain'), 'DmTestDomain', 'DmTestCateg has ancestor DmTestDomain');

  $t->isa_ok($post->getAncestorRecord('DmTestCateg'), 'DmTestCateg', 'DmTestPost has ancestor DmTestCateg');

  $t->isa_ok($post->getAncestorRecord('DmTestDomain'), 'DmTestDomain', 'DmTestPost has ancestor DmTestDomain');
  
  $t->isa_ok($comment->getAncestorRecord('DmTestPost'), 'DmTestPost', 'DmTestComment has ancestor DmTestPost');

  $t->isa_ok($comment->getAncestorRecord('DmTestCateg'), 'DmTestCateg', 'DmTestComment has ancestor DmTestCateg');

  $t->isa_ok($comment->getAncestorRecord('DmTestDomain'), 'DmTestDomain', 'DmTestComment has ancestor DmTestDomain');

  $t->is($fruit->getAncestorRecord('DmTestFruit'), $fruit, 'DmTestFruit ancestor is itself');

  $t->is($post->getAncestorRecord('DmTestPost'), $post, 'DmTestPost ancestor is itself');

$t->diag('Ancestor record id tests');

  try { $fruit->getAncestorRecordId('DmTestPost'); $ok = false; }
  catch(dmRecordException $e) { $ok = true; }
  $t->ok($ok, 'Fruit has no ancestor DmTestPost');

  try { $tag->getAncestorRecordId('DmTestCateg'); $ok = false; }
  catch(dmRecordException $e) { $ok = true; }
  $t->ok($ok, 'DmTestTag has no ancestor DmTestCateg');

$domainId = $categ->getAncestorRecord('DmTestDomain')->id;
  $t->is($categ->getAncestorRecordId('DmTestDomain'), $domainId, 'DmTestCateg has ancestor DmTestDomain id');

$categId = $post->getAncestorRecord('DmTestCateg')->id;
  $t->is($post->getAncestorRecordId('DmTestCateg'), $categId, 'DmTestPost has related DmTestCateg id');

$domainId = $post->getAncestorRecord('DmTestDomain')->id;
  $t->is($post->getAncestorRecordId('DmTestDomain'), $domainId, 'DmTestPost has related DmTestDomain id');

$categId = $comment->getAncestorRecord('DmTestCateg')->id;
  $t->is($comment->getAncestorRecordId('DmTestCateg'), $categId, 'DmTestComment has related DmTestCateg id');

$domainId = $comment->getAncestorRecord('DmTestDomain')->id;
  $t->is($comment->getAncestorRecordId('DmTestDomain'), $domainId, 'DmTestComment has related DmTestDomain id');

  $t->is($tag->getAncestorRecordId('DmTestTag'), $tag->id, 'DmTestTag has related DmTestTag id');

  $t->is($post->getAncestorRecordId('DmTestPost'), $post->id, 'DmTestPost has related DmTestPost id');