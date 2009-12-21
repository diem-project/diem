<?php

require_once(dirname(__FILE__).'/helper/dmMediaUnitTestHelper.php');
$helper = new dmMediaUnitTestHelper();
$helper->boot('admin');

$t = new lime_test();

$mediaTable  = dmDb::table('DmMedia');
$folderTable = dmDb::table('DmMediaFolder');

$t->diag('Media tests');

$root = $folderTable->checkRoot();

$t->diag('syncing root');
$root->sync();

$helper->checkTreeIntegrity($t);

$helper->testFolderCorrelations($t);

$parent = $folderTable->createQuery('f')
->select('f.*, RANDOM() as rand')
->orderBy('rand')
->fetchOne();

$name   = dmString::random();
$folder = $folderTable->create(array(
  'name' => $name,
  'relPath' => $parent->relPath.'/'.$name
));
$folder->Node->insertAsFirstChildOf($parent);

$t->is($folder->exists(), true, 'Folder created');

$t->is((string)$folder->Node->getParent(), (string)$parent, 'Folder inserted in its parent');

$helper->checkTreeIntegrity($t);

$helper->testFolderCorrelations($t);

require_once(dmOs::join(sfConfig::get('dm_admin_dir'), 'modules/dmMediaLibrary/lib/DmAdminEditMediaFolderForm.php'));

//$form = new DmAdminEditMediaFolderForm($folder);
//
//$values = array(
//  'id' => $folder->id,
//  'name' => $folder->name,
//  'parent_id' => $folder->getNodeParentId()
//);
//
//$t->comment('Rename the folder');
//
//$form = new DmAdminEditMediaFolderForm($folder);
//
//$t->comment('Submit bad name');
//
//$form->bind(array_merge($values, array('name' => '/')));
//
//$t->isnt($form->isValid(), 'Form is not valid');
//
//$t->comment('Submit same name');
//
//$form->bind(array_merge($values));
//
//$t->is($form->isValid(), 'Form is valid');
//
//$t->comment('Submit good name');
//
//$form->bind(array_merge($values, array('name' => dmString::random())));
//
//$t->is($form->isValid(), 'Form is valid');
//
//$folder = $form->save();

$folder->delete();

$helper->checkTreeIntegrity($t);

$helper->testFolderCorrelations($t);