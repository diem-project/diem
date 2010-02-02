<?php

require_once(dirname(__FILE__).'/helper/dmMediaUnitTestHelper.php');
$helper = new dmMediaUnitTestHelper();
$helper->boot();

$t = new lime_test();

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

$fileName = basename(__FILE__);
$filePath = dmOs::join($f1->fullPath, $fileName);
copy(__FILE__, $filePath);

$media = dmDb::table('DmMedia')->create(array(
  'file' => $fileName,
  'author' => 'Thibault D.',
  'legend' => 'dmMedia test cases',
  'dm_media_folder_id' => $f1->id
))->saveGet();

$t->ok($media->exists(), 'media has been saved');

$t->is($media->fullPath, $f1->fullPath.'/'.$media->file, 'Media full path is '.$media->fullPath);

$t->ok(file_exists($inF1Path = $f1->fullPath.'/'.$fileName), $inF1Path.' exists');
$t->ok(!file_exists($inF2Path = $f2->fullPath.'/'.$fileName), $inF2Path.' does not exist');

$t->comment('Test media->move');

$media->move($f2);

$t->is($media->fullPath, $f2->fullPath.'/'.$media->file, 'Media full path is '.$media->fullPath);

$t->ok(!file_exists($inF1Path = $f1->fullPath.'/'.$fileName), $inF1Path.' does no more exist');
$t->ok(file_exists($inF2Path = $f2->fullPath.'/'.$fileName), $inF2Path.' exists');