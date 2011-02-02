<?php

require_once(realpath(dirname(__FILE__).'/../../..').'/unit/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$nbLoremizeRecords = 10;
$helper->get('page_tree_watcher')->connect();
$markdown = $helper->get('markdown');
$nbTests = 5 + $nbLoremizeRecords*12;

$t = new lime_test($nbTests);

$helper->loremizeDatabase($nbLoremizeRecords, $t);

dmDb::table('DmAutoSeo')->findOneByModuleAndAction('dmTestDomain', 'show')->merge(array(
  'slug' => '%dmTestDomain.id%-%dmTestDomain%'
))->save();

dmDb::table('DmAutoSeo')->findOneByModuleAndAction('dmTestCateg', 'show')->merge(array(
  'slug' => '%dmTestCateg.id%-%dmTestCateg%'
))->save();

dmDb::table('DmAutoSeo')->findOneByModuleAndAction('dmTestPost', 'show')->merge(array(
  'slug'      => '%dmTestPost%-%dmTestPost.id%',
  'name'      => 'Post : %dmTestPost.title%',
  'title'     => '%dmTestPost% | %dmTestCateg.name%',
  'h1'        => '%dmTestPost%',
  'description' => '%dmTestPost.body%'
))->save();

$t->diag('seo construction');

$timer = dmDebug::timer('update seo');

try
{
  $helper->updatePageTreeWatcher($t);
}
catch(Exception $e)
{
  $t->skip('Not supported on this server: '.$e->getMessage(), $nbTests);
  return;
}

$t->ok(true, sprintf('Seo updated in %01.2f s', $timer->getElapsedTime()));

foreach(dmDb::table('dmTestPost')->findAll() as $post)
{
  if (!$page = $post->getDmPage())
  {
    $t->skip('Post '.$post.' has no page', 6);
    continue;
  }

  $page->refresh(true);

  $categ = $page->getNode()->getParent()->getRecord();
  $domain = $page->getNode()->getParent()->getNode()->getParent()->getRecord();

  $t->is($post->isActive, $page->isActive, 'is_active field synchronized to '.($post->isActive ? 'TRUE' : 'FALSE'));

  $slug = 'dm-test-domains/'.$domain->id.'-'.dmString::slugify($domain->title).'/'.$categ->id.'-'.dmString::slugify($categ->name).'/'.dmString::slugify($post->title).'-'.$post->id;
  $slug = dmSeoSynchronizer::truncateValueForField($slug, 'slug');
    $t->is($page->slug, $slug, 'slug : '.$slug);

  $name = 'Post : '.trim($post->title);
  $name = dmSeoSynchronizer::truncateValueForField($name, 'name');
    $t->is($page->name, $name, 'name : '.$name);

  $title = ucfirst(trim($post->title).' | '.trim($categ->name));
  $title = dmSeoSynchronizer::truncateValueForField($title, 'title');
    $t->is($page->title, $title, 'title : '.$title);

  $h1 = trim($post->title);
  $h1 = dmSeoSynchronizer::truncateValueForField($h1, 'h1');
    $t->is($page->h1, $h1, 'h1 : '.$h1);

  $description = dmSeoSynchronizer::truncateValueForField(dmMarkdown::brutalToText($post->body), 'description');
    $t->is($page->description, $description ? $description : null, 'description : '.$description);
}

$t->comment('Add inactive domain');
$domain = dmDb::table('DmTestDomain')->create(array(
  'title' => 'a domain',
  'is_active' => false
))->saveGet();

$domain->refresh();

$t->comment('created domain '.$domain->id);

$helper->updatePageTreeWatcher($t);

$t->isa_ok($page = $domain->dmPage, 'DmPage', 'domain has a page');
$t->ok(!$page->isActive, 'domain page is not active');