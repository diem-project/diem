<?php

require_once(dirname(__FILE__).'/helper/dmMediaUnitTestHelper.php');
$helper = new dmMediaUnitTestHelper();
$helper->boot('admin');

$t = new lime_test(11);

$mediaTable  = dmDb::table('DmMedia');
$folderTable = dmDb::table('DmMediaFolder');

$t->diag('Media tests');

$folderTable->checkRoot();

$root = $folderTable->getTree()->fetchRoot();

$t->diag('syncing root');
$root->sync();

$helper->checkTreeIntegrity($t);

$helper->testFolderCorrelations($t);

$parent = $folderTable->createQuery('f')->orderBy('RANDOM()')->fetchOne();
$name   = dmString::random();
$folder = $folderTable->create(array(
  'name' => $name,
  'relPath' => $parent->relPath.'/'.$name
));
$folder->Node->insertAsFirstChildOf($parent);

$t->is($folder->exists(), true, 'Folder created');

$t->is($folder->Node->getParent(), $parent, 'Folder inserted in its parent');

$helper->checkTreeIntegrity($t);

$helper->testFolderCorrelations($t);

require_once(dmOs::join(sfConfig::get('dm_admin_dir'), 'modules/dmMediaLibrary/lib/DmAdminEditMediaFolderForm.php'));

$form = new DmAdminEditMediaFolderForm($folder);

$values = $form->getDefaults();

$t->comment('Not completed');

$folder->delete();

$helper->checkTreeIntegrity($t);

$helper->testFolderCorrelations($t);

//$t->comment('Submit empty form');
//$form->bind($values);
//
//$t->is($form->isValid(), true, 'The form is valid');
//
//$t->comment('Submit bad dir name');
//$values['name'] = '/';
//$form->bind($values);
//
//$t->is($form->isValid(), false, 'The form is not valid');
//
//$t->comment('Submit good dir name');
//$values['name'] = $name = dmString::random();
//$form->bind($values);
//
//$t->is($form->isValid(), true, 'The form is valid');
//if (!$form->isValid())
//{
//  $t->comment($form->getErrorSchema()->getMessage());
//}