<?php

require_once(dirname(__FILE__).'/helper/dmMediaUnitTestHelper.php');
$helper = new dmMediaUnitTestHelper();
$helper->boot();

$t = new lime_test(122);

$table = dmDb::table('DmMediaFolder');
$root = $table->checkRoot();

$t->diag('syncing root');
$root->sync();

$helper->checkTreeIntegrity($t);

$helper->testFolderCorrelations($t);

$grandParent = $table->createQuery('f')
->select('f.*, RANDOM() as rand')
->orderBy('rand')
->fetchOne();

$parent = $table->create(array(
  'rel_path' => $grandParent->relPath.'/parent'
));
$parent->Node->insertAsFirstChildOf($grandParent);

$t->is($parent->exists(), true, 'Folder parent created');
$t->is((string)$parent->Node->getParent(), (string)$grandParent, 'Folder parent inserted in grand-parent');

$f1 = $table->create(array(
  'rel_path' => $parent->relPath.'/f1'
));
$f1->Node->insertAsFirstChildOf($parent);

$t->is($f1->exists(), true, 'Folder f1 created');
$t->is((string)$f1->Node->getParent(), (string)$parent, 'Folder f1 inserted in parent');

$f2 = $table->create(array(
  'rel_path' => $parent->relPath.'/f2'
));
$f2->Node->insertAsFirstChildOf($parent);

$t->is($f2->exists(), true, 'Folder f2 created');
$t->is((string)$f2->Node->getParent(), (string)$parent, 'Folder f2 inserted in parent');

$f3 = $table->create(array(
  'rel_path' => $f2->relPath.'/f3'
));
$f3->Node->insertAsFirstChildOf($f2);

$t->is($f3->exists(), true, 'Folder f3 created');
$t->is((string)$f3->Node->getParent(), (string)$f2, 'Folder f3 inserted in f2');

$helper->checkTreeIntegrity($t);

$helper->testFolderCorrelations($t);

$t->comment('getName()');

$t->is($table->getTree()->fetchRoot()->name, 'root', 'Root name is root');

$t->is($f1->name, 'f1', 'f1 name is f1');
$t->is($f2->name, 'f2', 'f2 name is f2');

$t->comment('hasSubfolder()');

$t->ok($parent->hasSubfolder($f1->name), 'Parent has subfolder f1');
$t->ok($parent->hasSubfolder($f2->name), 'Parent has subfolder f2');
$t->ok($f2->hasSubfolder($f3->name), 'f2 has subfolder f3');

$t->ok(!$f1->hasSubfolder($f2->name), 'f1 has no subfolder f2');
$t->ok(!$f1->hasSubfolder($root->name), 'f1 has no subfolder root');
$t->ok(!$parent->hasSubfolder($f3), 'parent has no subfolder f3');

$t->comment('getFullPath');

$t->is($f1->getFullPath(), dmOs::join(sfConfig::get('sf_upload_dir'), $f1->get('rel_path')), 'f1 full path is '.$f1->fullPath);
$t->is($f1->fullPath, dmOs::join(sfConfig::get('sf_upload_dir'), $f1->relPath), 'f1 full path is '.$f1->fullPath);

$t->comment('getNbElements');

$t->is($f1->nbElements, 0, 'f1 has 0 elements');
$t->is($f2->nbElements, 1, 'f2 has 1 element');
$t->is($parent->nbElements, 2, 'parent has 2 element');

$t->comment('getSubFoldersByName()');

$t->is($parent->getSubFoldersByName(), array(
  'f1' => $f1,
  'f2' => $f2
), 'f1 folders by name are f1 & f2');

$t->is($f1->getSubFoldersByName(), array(), 'f1 folders by name is empty');

$t->is($f2->getSubFoldersByName(), array(
  'f3' => $f3
), 'f3 folders by name is f3');

$t->comment('getDmMediasByFileName()');

$t->is($f1->getDmMediasByFileName(), array(), 'f1 medias by name is empty');

$t->comment('getMedias()');

$t->is($f1->getMedias()->getData(), array(), 'f1 medias is empty');

$t->comment('misc');

foreach(array($grandParent, $parent, $f1, $f2) as $folder)
{
  $t->ok($folder->dirExists(), $folder->name.' dir exists');
  $t->ok($folder->isWritable(), $folder->name.' isWritable');
  $t->is($folder->getNodeParentId(), $folder->isRoot() ? null : $folder->Node->getParent()->id, $folder->name.' node parent id is '.$folder->getNodeParentId());
}

$t->ok(!$f2->hasFile('test.jpg'), 'f2 has no file test.jpg');

$t->comment('Add a file in f2');
copy(
  dmOs::join(sfConfig::get('dm_core_dir'), 'data/image/defaultMedia.jpg'),
  $f2->fullPath.'/test.jpg'
);
$f2->sync();

$media = dmDb::table('DmMedia')->findOneByFileAndDmMediaFolderId('test.jpg', $f2->id);

$t->ok($media->exists(), 'media exists');
$t->is($media->Folder, $f2, 'media folder is f2');

$t->ok($f2->hasFile('test.jpg'), 'f2 has file test.jpg');

$t->is($f2->nbElements, 2, 'f2 has 2 element');

$t->is($f2->getDmMediasByFileName(), array(
  'test.jpg' => $media
), 'f2 medias by name is array($media)');

$t->is($f2->getMedias()->getData(), array($media), 'f2 medias is array($media)');

$t->comment('rename');

try
{
  $table->getTree()->fetchRoot()->rename('root_new_name');
  $t->fail('Root folder cannot be moved');
}
catch(dmException $e)
{
  $t->pass('Root folder cannot be moved');
}

$message = 'f1 folder cannot be moved to bad/name';
try
{
  $f1->rename('bad/name');
  $t->fail($message);
}
catch(dmException $e)
{
  $t->pass($message);
}
catch(Exception $e)
{
  $t->fail($message);
}

$message = 'f1 folder cannot be moved to '.$f2->name;
try
{
  $f1->rename($f2->name);
  $t->fail($message);
}
catch(dmException $e)
{
  $t->pass($message);
}
catch(Exception $e)
{
  $t->fail($message);
}

$message = 'f2 folder cannot be moved to '.$f1->name;
try
{
  $f2->rename($f1->name);
  $t->fail($message);
}
catch(dmException $e)
{
  $t->pass($message);
}
catch(Exception $e)
{
  $t->fail($message);
}

$message = 'f2 move to f2b';
try
{
  $f2->rename('f2b');
  $t->pass($message);
}
catch(Exception $e)
{
  $t->fail($message.' : '.$e->getMessage());
  throw $e;
}

$helper->checkTreeIntegrity($t);

$helper->testFolderCorrelations($t);

$t->is($f2->relPath, $parent->relPath.'/f2b', 'f2 rel path: '.$f2->relPath);
$t->is($f2->fullPath, $parent->fullPath.'/f2b', 'f2 full path: '.$f2->fullPath);

$t->is($media->relPath, $f2->relPath.'/test.jpg', 'media rel path: '.$media->relPath);
$t->is($media->fullPath, $f2->fullPath.'/test.jpg', 'media full path: '.$media->fullPath);

$t->is($f3->relPath, $parent->relPath.'/f2b/f3', 'f3 rel path: '.$f3->relPath);
$t->is($f3->fullPath, $parent->fullPath.'/f2b/f3', 'f3 full path: '.$f3->fullPath);

$t->ok(!file_exists($parent->fullPath.'/f2/test.jpg'), 'old file does no more exist');
$t->ok(!is_dir($parent->fullPath.'/f2/f3'), 'old f3 dir does no more exist');
$t->ok(!is_dir($parent->fullPath.'/f2'), 'old f2 dir does no more exist');

$t->ok(file_exists($parent->fullPath.'/f2b/test.jpg'), 'new file exists');
$t->ok(is_dir($parent->fullPath.'/f2b/f3'), 'new f3 dir exists');
$t->ok(is_dir($parent->fullPath.'/f2b'), 'new f2 dir exists');

$t->ok($f2->dirExists(), $f2->name.' dir exists');
$t->ok($f2->isWritable(), $f2->name.' isWritable');

$message = 'parent move to parent_new';
try
{
  $parent->rename('parent_new');
  $t->pass($message);
}
catch(Exception $e)
{
  $t->fail($message.' : '.$e->getMessage());
  throw $e;
}

$helper->checkTreeIntegrity($t);

$helper->testFolderCorrelations($t);

$t->is($parent->relPath, trim($grandParent->relPath.'/parent_new', '/'), 'parent rel path: '.$parent->relPath);
$t->is($parent->fullPath, $grandParent->fullPath.'/parent_new', 'parent full path: '.$parent->fullPath);

$t->is($f2->relPath, $parent->relPath.'/f2b', 'f2 rel path: '.$f2->relPath);
$t->is($f2->fullPath, $parent->fullPath.'/f2b', 'f2 full path: '.$f2->fullPath);

$t->is($media->relPath, $f2->relPath.'/test.jpg', 'media rel path: '.$media->relPath);
$t->is($media->fullPath, $f2->fullPath.'/test.jpg', 'media full path: '.$media->fullPath);

$t->is($f3->relPath, $parent->relPath.'/f2b/f3', 'f3 rel path: '.$f3->relPath);
$t->is($f3->fullPath, $parent->fullPath.'/f2b/f3', 'f3 full path: '.$f3->fullPath);

$t->ok(!file_exists($grandParent->fullPath.'/parent/f2b/test.jpg'), 'old file does no more exist');
$t->ok(!is_dir($grandParent->fullPath.'/parent/f2b/f3'), 'old f3 dir does no more exist');
$t->ok(!is_dir($grandParent->fullPath.'/parent/f2b'), 'old f2 dir does no more exist');
$t->ok(!is_dir($grandParent->fullPath.'/parent'), 'old parent dir does no more exist');

$t->ok(file_exists($parent->fullPath.'/f2b/test.jpg'), 'new file exists');
$t->ok(is_dir($parent->fullPath.'/f2b/f3'), 'new f3 dir exists');
$t->ok(is_dir($parent->fullPath.'/f2b'), 'new f2 dir exists');
$t->ok(is_dir($parent->fullPath), 'new parent dir exists');

$t->ok($parent->dirExists(), $parent->name.' dir exists');
$t->ok($parent->isWritable(), $parent->name.' isWritable');

$t->comment('move');

$message = 'Root folder cannot be moved';
try
{
  $table->getTree()->fetchRoot()->move($f1);
  $t->fail($message);
}
catch(dmException $e)
{
  $t->pass($message);
}
catch(Exception $e)
{
  $t->fail($message);
}

$message = 'f2 can not be moved to f3';
try
{
  $f2->move($f3);
  $t->fail($message);
}
catch(dmException $e)
{
  $t->pass($message);
}
catch(Exception $e)
{
  $t->fail($message);
}

$message = 'f2 can not be moved to f2';
try
{
  $f2->move($f2);
  $t->fail($message);
}
catch(dmException $e)
{
  $t->pass($message);
}
catch(Exception $e)
{
  $t->fail($message);
}

$message = 'move f2 to f1';
try
{
  $f2->move($f1);
  $t->pass($message);
}
catch(Exception $e)
{
  $t->fail($message);
  throw $e;
}

$helper->checkTreeIntegrity($t);

$helper->testFolderCorrelations($t);

$t->is((string)$f2->getNode()->getParent(), (string)$f1, 'f2 is in f1');

$t->ok($f1->hasSubfolder($f2->name));

$t->is($f1->nbElements, 1, 'f1 has one element');

$t->is($f1->getSubFoldersByName(), array(
  'f2b' => $f2
), 'f1 folders by name is f2');

$t->is($f2->relPath, $parent->relPath.'/f1/f2b', 'f2 rel path: '.$f2->relPath);
$t->is($f2->fullPath, $parent->fullPath.'/f1/f2b', 'f2 full path: '.$f2->fullPath);

$t->is($media->relPath, $f2->relPath.'/test.jpg', 'media rel path: '.$media->relPath);
$t->is($media->fullPath, $f2->fullPath.'/test.jpg', 'media full path: '.$media->fullPath);

$t->is($f3->relPath, $parent->relPath.'/f1/f2b/f3', 'f3 rel path: '.$f3->relPath);
$t->is($f3->fullPath, $parent->fullPath.'/f1/f2b/f3', 'f3 full path: '.$f3->fullPath);

$t->ok(!file_exists($parent->fullPath.'/f2b/test.jpg'), 'old file does no more exist');
$t->ok(!is_dir($parent->fullPath.'/f2b/f3'), 'old f3 dir does no more exist');
$t->ok(!is_dir($parent->fullPath.'/f2b'), 'old f2 dir does no more exist');

$t->ok(file_exists($parent->fullPath.'/f1/f2b/test.jpg'), 'new file exists');
$t->ok(is_dir($parent->fullPath.'/f1/f2b/f3'), 'new f3 dir exists');
$t->ok(is_dir($parent->fullPath.'/f1/f2b'), 'new f2 dir exists');

$t->ok($f2->dirExists(), $f2->name.' dir exists');
$t->ok($f2->isWritable(), $f2->name.' isWritable');

$t->comment('Add a .thumbs folder in f2');

mkdir($f2->fullPath.'/.thumbs');
$f2->sync();

$t->is(dmDb::query('DmMediaFolder f')->where('f.rel_path LIKE ?', '%.thumbs')->count(), 0, 'No folder .thumbs where created');

$helper->get('filesystem')->sf('doctrine:dql "from DmMediaFolder folder" --table');
$t->comment($helper->get('filesystem')->getLastExec('output'));