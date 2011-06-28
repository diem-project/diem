<?php

require_once(dirname(__FILE__).'/helper/dmMediaUnitTestHelper.php');
$helper = new dmMediaUnitTestHelper();
$helper->boot('admin');

$t = new lime_test();

$table = dmDb::table('DmMediaFolder');

$t->diag('Media tests');

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

$helper->checkTreeIntegrity($t);

$helper->testFolderCorrelations($t);

require_once(dmOs::join(sfConfig::get('dm_admin_dir'), 'modules/dmMediaLibrary/lib/DmAdminRenameMediaFolderForm.php'));

$form = new DmAdminRenameMediaFolderForm($f1);
$f1FullPath = $f1->fullPath;

$t->is($form->getDefault('id'), $f1->id, 'Form default id is f1->id');
$t->is($form->getDefault('name'), $f1->name, 'Form default name is f1->name');

$t->comment('Submit unchanged form');

$form->bind(array('name' => $f1->name));

$t->ok($form->isValid(), 'The form is valid');

if(!$form->isValid())
{
  $form->throwError();
}

$t->comment('Save unchanged form');

$form->save();

$t->is($f1->fullPath, $f1FullPath, $f1->fullPath);
$t->is($f1->exists(), true, 'Folder f2 exists');

$t->comment('Submit form with already existing name');

$form->bind(array('name' => $f2->name));

$t->ok(!$form->isValid(), 'The form is not valid');

$t->comment('Submit form with bad name');

$form->bind(array('name' => '£/'));

$t->ok(!$form->isValid(), 'The form is not valid');

$t->comment('Submit form with good name');

$form->bind(array('name' => 'f1bis'));

$t->ok($form->isValid(), 'The form is valid');

$t->comment('Save changed form');

$form->save();

$t->is($f1->name, 'f1bis', $f1->name);
$t->is($f1->fullPath, $parent->fullPath.'/f1bis', $f1->fullPath);
$t->is($f1->exists(), true, 'Folder f1bis exists');
$t->ok(!is_dir($f1FullPath), 'Folder f1 does no more exist');

$t->comment('Renaming a folder which has children');

$form = new DmAdminRenameMediaFolderForm($parent);
$parentFullPath = $parent->fullPath;
$f1bisFullPath = $f1->fullPath;
$f2FullPath = $f2->fullPath;

$form->bind(array('name' => 'parentbis'));

$t->ok($form->isValid(), 'form is valid');

$form->save();

$t->is($parent->name, 'parentbis', $parent->name);
$t->is($parent->fullPath, $grandParent->fullPath.'/parentbis', $parent->fullPath);
$t->is($parent->exists(), true, 'Folder parentbis exists');
$t->ok(!is_dir($parentFullPath), 'Folder parent does no more exist');

$t->ok(!is_dir($f1bisFullPath), 'Folder f1bis does no more exist');
$t->is($f1->fullPath, $parent->fullPath.'/f1bis', 'f1bis full path is '.$f1->fullPath);
$t->ok($f1->exists(), 'Moved folder f1bis exists');

$t->ok(!is_dir($f2FullPath), 'Folder f2 does no more exist');
$t->is($f2->fullPath, $parent->fullPath.'/f2', 'f2 full path is '.$f2->fullPath);
$t->ok($f2->exists(), 'Moved folder f2 exists');

$helper->checkTreeIntegrity($t);

$helper->testFolderCorrelations($t);