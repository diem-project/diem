<?php

require_once(realpath(dirname(__FILE__).'/../../..').'/unit/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test(36);

$helper->clearDatabase($t);

$loremizer = $helper->get('record_loremizer');

$t->is(dmDb::table('DmTestPost')->count(), 0, 'No post exist');

for($it=1; $it<4; $it++)
{
  $t->comment('Loremize a new comment');
  $comment = $loremizer->execute(new DmTestComment());
  
  $t->comment('Save generated comment');
  $comment->save();
  
  $t->ok($comment->exists(), 'The comment has been saved');
  
  $t->is(dmDb::table('DmTestComment')->count(), $it, $it.' comments exist');
  
  $post = $comment->Post;
  
  $t->ok($post->exists(), 'A post has been created');
  
  $t->is(dmDb::table('DmTestPost')->count(), 1, 'Only one post exists');
  $t->is(dmDb::table('DmTestCateg')->count(), 1, 'Only one categ exists');
  
  $v = new dmValidatorLinkUrl(array('required' => false));
  $t->is($post->url, $v->clean($post->url), 'Post url is valid: '.$post->url);
  
  $categ = $post->Categ;
  
  $t->ok($categ->exists(), 'A categ has been created');
  
  $t->is($categ->Domains->count(), 0, 'The categ has no domain');
  
  $t->is(dmDb::table('DmTestDomain')->count(), 0, 'No domain exist');
}

$t->is(dmDb::table('DmTestFruit')->count(), 0, 'No fruit exist');

$t->comment('Delete categ');
$categ->delete();

$t->comment('Loremize a new domain');
$domain = $loremizer->execute(new DmTestDomain());

$t->ok($domain->hasCurrentTranslation(), 'domain has a current translation');

$t->comment('Save generated domain');
$domain->save();

$t->ok($domain->exists(), 'The domain exists');

$t->ok($domain->title, 'The domain title is '.$domain->title);

$t->is($domain->Categs->count(), 0, 'domain has no categs');

$t->comment('Generate 50 categs');
for($it=0; $it<50; $it++)
{
  $loremizer->execute(new DmTestCateg())->save();
}

$t->is(dmDb::table('DmTestCateg')->count(), 50, '50 categs exist');

$categ = dmDb::query('DmTestCateg c')->withI18n()->fetchOne();
$nbCategsByName = dmDb::query('DmTestCateg c')->withI18n()->where('cTranslation.name = ?', $categ->name)->count();

$t->is($nbCategsByName, 1, 'Each categ name is unique');

$t->ok($count = dmDb::table('DmTestDomainCateg')->count(), 'Some domain-categ relations have been created: '.$count);