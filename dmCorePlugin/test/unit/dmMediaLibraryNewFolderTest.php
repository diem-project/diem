<?php

require_once(dirname(__FILE__).'/helper/dmMediaUnitTestHelper.php');
$helper = new dmMediaUnitTestHelper();
$helper->boot('admin');

$t = new lime_test(19);

$mediaTable  = dmDb::table('DmMedia');
$folderTable = dmDb::table('DmMediaFolder');

$t->diag('Media tests');

$folderTable->checkRoot();

$root = $folderTable->getTree()->fetchRoot();

$t->diag('syncing root');
$root->sync();

$helper->checkTreeIntegrity($t);

$helper->testFolderCorrelations($t);

$parent = $folderTable->createQuery('f')
->select('f.*, RANDOM() as rand')
->orderBy('rand')
->fetchOne();

require_once(dmOs::join(sfConfig::get('dm_admin_dir'), 'modules/dmMediaLibrary/lib/DmAdminNewMediaFolderForm.php'));

$form = new DmAdminNewMediaFolderForm();
$form->setDefault('parent_id', $parent->id);

$values = array('parent_id' => $parent->id);

$t->comment('Submit empty form');
$form->bind($values);

$t->is($form->isValid(), false, 'The form is not valid');

$t->comment('Submit bad dir name');
$values['name'] = '/';
$form->bind($values);

$t->is($form->isValid(), false, 'The form is not valid');

$t->comment('Submit good dir name');
$values['name'] = $name = dmString::random();
$form->bind($values);

$t->is($form->isValid(), true, 'The form is valid');
if (!$form->isValid())
{
  $t->comment($form->getErrorSchema()->getMessage());
}

$t->is($form->getValue('name'), $name, 'The name is '.$name);

$t->is($form->getValue('rel_path'), $relPath = trim($parent->relPath.'/'.$name, '/'), 'The rel path is '.$relPath);

$t->is($form->getObject()->exists(), false, 'The folder does not exist');

$folder = $form->updateObject()->saveGet();

$t->is($folder->exists(), true, 'The folder not exists');

$helper->checkTreeIntegrity($t);

$helper->testFolderCorrelations($t);

$t->comment('Try to create a folder with the same name');

$form = new DmAdminNewMediaFolderForm();
$form->setDefault('parent_id', $parent->id);

$values = array('parent_id' => $parent->id);

$t->comment('Submit empty form');
$form->bind($values);

$t->is($form->isValid(), false, 'The form is not valid');

$t->comment('Submit same dir name: '.$folder->name);
$values['name'] = $folder->name;
$form->bind($values);

$t->is($form->isValid(), false, 'The form is not valid');

$folder->Node->delete();

$t->is($folder->exists(), false, 'The folder does no more exist');

$helper->checkTreeIntegrity($t);

$helper->testFolderCorrelations($t);