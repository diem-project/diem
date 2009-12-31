<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot('front');

$t = new lime_test(76);

$pageTable = dmDb::table('DmPage');
$root = $pageTable->getTree()->fetchRoot();

$testModule = dmString::random();

$pages = array($root);

for($it=1; $it<=2; $it++)
{
  $pages[] = $p = dmDb::table('DmPage')->create(array(
    'module'  => $testModule,
    'action'  => 'test'.$it,
    'name'    => dmString::random(),
    'slug'    => dmString::random()
  ));
  $p->Node->insertAsLastChildOf($pages[$it-1]);

  $pages[$it-1]->refresh();
  $p->refresh();

  $t->ok($p->exists(), $p.' exists');
  $t->is($p->nodeParentId, (string)$pages[$it-1]->id, $p.' is child of '.($pages[$it-1]));
}

dm::loadHelpers(array('DmFront'));

foreach($pages as $index => $page)
{
  $helper->get('context')->setPage($page);
  
  $t->comment('Testing ->isSource for '.$page);

  foreach($pages as $_page)
  {
    $ok = ($page === $_page);
    
    $t->is($page->isSource($_page), $ok, '$page->isSource($page) '.($ok ? 'TRUE' : 'FALSE'));
    $t->is($page->isSource("page:".$_page->id), $ok, '$page->isSource("page:".$page->id) '.($ok ? 'TRUE' : 'FALSE'));
    $t->is($page->isSource($_page->module."/".$_page->action), $ok, '$page->isSource($page->module."/".$page->action) '.($ok ? 'TRUE' : 'FALSE'));
    $t->is(dm_current($_page), $ok, 'dm_current($page) '.($ok ? 'TRUE' : 'FALSE'));
  }

  if($index)
  {
    $t->comment('Testing ->isDescendantOfSource for '.$page);

    for($i=0; $i<$index;$i++)
    {
      $t->ok($page->isDescendantOfSource($pages[$i]), '$page->isDescendantOfSource($pages[$i])');
      $t->ok($page->isDescendantOfSource("page:".$pages[$i]->id), '$page->isDescendantOfSource("page:".$pages[$i]->id)');
      $t->ok($page->isDescendantOfSource($pages[$i]->module."/".$pages[$i]->action), '$page->isDescendantOfSource($pages[$i]->module."/".$pages[$i]->action)');
      $t->ok(dm_parent($pages[$i]), 'dm_parent($_page)');
    }
  }
  
  $t->comment('Testing NOT ->isDescendantOfSource for '.$page);

  for($i=$index; $i<3;$i++)
  {
    $t->ok(!$page->isDescendantOfSource($pages[$i]), '!$page->isDescendantOfSource($pages[$i])');
    $t->ok(!$page->isDescendantOfSource("page:".$pages[$i]->id), '!$page->isDescendantOfSource("page:".$pages[$i]->id)');
    $t->ok(!$page->isDescendantOfSource($pages[$i]->module."/".$pages[$i]->action), '!$page->isDescendantOfSource($pages[$i]->module."/".$pages[$i]->action)');
    $t->ok(!dm_parent($pages[$i]), 'dm_parent($_page)');
  }
}

/*
 * Clean up
 */

foreach($pages as $page)
{
  if(!$page->Node->isRoot())
  {
    $p->Node->delete();
  }
}