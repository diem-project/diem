<?php

require_once(dirname(__FILE__).'/helper/dmMediaUnitTestHelper.php');
$helper = new dmMediaUnitTestHelper();
$helper->boot();

$t = new lime_test(58);

$mediaTable  = dmDb::table('DmMedia');
$folderTable = dmDb::table('DmMediaFolder');

$t->diag('Media tests');

$folderTable->checkRoot();

$root = $folderTable->getTree()->fetchRoot();

$t->diag('syncing root');
$root->sync();

$helper->checkTreeIntegrity($t);

$helper->testFolderCorrelations($t);

$t->isa_ok($root, 'DmMediaFolder', 'root is a media folder');

$t->is($root->fullPath, dmOs::normalize(sfConfig::get('sf_upload_dir')), 'root full path is '.$root->fullPath);

$t->diag('add a folder in root');

$folder = new DmMediaFolder;
$folder->relPath = 'test_'.dmString::random(8);
$folder->getNode()->insertAsLastChildOf($root);

$helper->checkTreeIntegrity($t);

$t->ok($folder->exists(), 'folder '.$folder->name.' has been created');

$t->is($folder->getNode()->getParent(), $root, 'folder\'s parent is root');

$t->is($folder->fullPath, dmOs::join(sfConfig::get('sf_upload_dir'), $folder->name), 'folder\'s full path is '.$folder->fullPath);

$t->ok(is_dir($folder->fullPath), 'folder exists in filesystem');

$t->diag('add a file in folder');

$fileName = dmString::random(8).'_'.basename(__FILE__);
$filePath = dmOs::join($folder->fullPath, $fileName);
copy(__FILE__, $filePath);

$media = $mediaTable->create(array(
  'file' => basename($filePath),
  'author' => 'Thibault D.',
  'legend' => 'dmMedia test cases',
  'dm_media_folder_id' => $folder->id
))->saveGet();

$t->ok($media->exists(), 'media has been saved');

$t->is($media->fullPath, $folder->fullPath.'/'.$media->file, 'Media full path is '.$media->fullPath);

$t->is($media->relPath, $folder->relPath.'/'.$media->file, 'Media rel path is '.$media->relPath);

$t->is($media->webPath, 'uploads/'.$media->relPath, 'Media web path is '.$media->webPath);

$t->is($media->Folder, $folder, 'media folder is folder');

$t->is($media->mime, 'application/x-httpd-php', 'media type is application/x-httpd-php');

$t->is($media->size, filesize(__FILE__), 'file size is '.filesize(__FILE__));

$t->is($media->file, $fileName, 'file file is '.$fileName);

$t->is($media->legend, 'dmMedia test cases', 'media legend is "dmMedia test cases"');

$t->diag('Delete media in db');

$media->delete();

$t->ok(!$media->exists(), 'media has been deleted in db');

$t->ok(!file_exists($media->fullPath), 'media has been deleted in filesystem');

$t->diag('create other media');

$filePath = str_replace('.php', '2.php', $filePath);
copy(__FILE__, $filePath);

$media = $mediaTable->create(array(
  'file' => basename($filePath),
  'author' => 'Thibault D.',
  'legend' => 'dmMedia test',
  'dm_media_folder_id' => $folder->id
))->saveGet();

$t->ok($media->exists(), 'media has been saved');

$t->is($media->mime, 'application/x-httpd-php', 'mime is application/x-httpd-php');

$t->is($media->mimeGroup, 'application', 'mime group is application');

$t->diag('Delete media in filesystem');

unlink($media->fullPath);

$t->diag('Sync folder');

$root->sync();

$helper->checkTreeIntegrity($t);

$t->ok(!$media->exists(), 'media has been deleted in database');

$t->ok(!file_exists($media->fullPath), 'media has been deleted in filesystem');

$t->diag('Sync root folder');

$root->sync();

$helper->checkTreeIntegrity($t);

$helper->testFolderCorrelations($t);

$t->diag('Copy heavy folders structure into folder');

$sourcePath = dmOs::join(dm::getDir(), 'dmCorePlugin/lib/view');
$destRelPath = $folder->relPath . '/' . 'test_lib_view_'.dmString::random(8);
$destFullPath = dmOs::join($root->fullPath, $destRelPath);

try
{
  $helper->get('filesystem')->mirror($sourcePath, $destFullPath, sfFinder::type('all'));
  $t->pass('Copy completed');
}
catch(Exception $e)
{
  $t->fail('Copy failed : '.$e->getMessage());
}

$t->ok(file_exists(dmOs::join($destFullPath, 'html/link/dmBaseLinkTag.php')), dmOs::join($destFullPath, 'html/link/dmBaseLinkTag.php').' exists');

$t->diag('Sync root');

$root->sync();

$helper->checkTreeIntegrity($t);

$helper->testFolderCorrelations($t);

$linkFolder = $folderTable->findOneByRelPath($destRelPath.'/html/link');

$t->isa_ok($linkFolder, 'DmMediaFolder', $destRelPath.'/html/link/ is a DmMediaFolder');

try
{
	$deepMedia = $mediaTable->findOneByFileAndDmMediaFolderId('dmBaseLinkTag.php', $linkFolder->id);

	$t->isa_ok($deepMedia, 'DmMedia', 'deep media found in db');
}
catch(Exception $e)
{
	$t->fail($e->getMessage());
}

$folderFullPath = $folder->fullPath;

$folder->getNode()->delete();

$t->ok(!$folder->exists(), 'folder '.$folder.' has been deleted in database');

$t->ok(!is_dir($folderFullPath), 'folder '.$folderFullPath.' has been deleted in filesystem');

$t->ok(!$folderTable->findOneByRelPath($folder->relPath), 'folder '.$folder.' can not be found anymore in db');

$t->diag('Sync root');

$root->sync();

$helper->checkTreeIntegrity($t);

$helper->testFolderCorrelations($t);

$t->diag('Copy heavy folders structure into folder');

$sourcePath = dmOs::join(dm::getDir(), 'dmCorePlugin/lib/model/doctrine');
$destRelPath = 'test_lib_model_'.dmString::random(8);
$destFullPath = dmOs::join($root->fullPath, $destRelPath);

try
{
  $helper->get('filesystem')->mirror($sourcePath, $destFullPath, sfFinder::type('all'));
  $t->pass('Copy completed');
}
catch(Exception $e)
{
  $t->fail('Copy failed : '.$e->getMessage());
}

$t->ok(file_exists(dmOs::join($destFullPath, 'PluginDmMediaTable.class.php')), dmProject::unRootify(dmOs::join($destFullPath, 'PluginDmMediaTable.class.php')).' exists');

$t->diag('Sync root');

$root->sync();

$helper->checkTreeIntegrity($t);

$helper->testFolderCorrelations($t);

$folder = $folderTable->findOneByRelPath($destRelPath);
$folderFullPath = $folder->fullPath;

$t->is($folderFullPath, dmOs::join($root->fullPath, $destRelPath));

$mediaFolder = $folderTable->findOneByRelPath($destRelPath);

$t->isa_ok($mediaFolder, 'DmMediaFolder', $destRelPath.'/media is a DmMediaFolder');

try
{
  $deepMedia = $mediaTable->findOneByFileAndDmMediaFolderId('PluginDmMediaTable.class.php', $mediaFolder->id);

  $t->isa_ok($deepMedia, 'DmMedia', 'deep media found in db');
}
catch(Exception $e)
{
  $t->fail($e->getMessage());
}

$t->diag('Destroy '.$destRelPath.' in fs');

$helper->get('filesystem')->unlink($destFullPath);

$t->ok(!is_dir($destFullPath), $destFullPath.' destroyed');

$root->sync();

$helper->checkTreeIntegrity($t);

$helper->testFolderCorrelations($t);

$t->ok(!$folder->exists(), 'folder '.$folder.' has been deleted in database');

$t->ok(!is_dir($folderFullPath), 'folder '.$folderFullPath.' has been deleted in filesystem');

$t->ok(!$folderTable->findOneByRelPath($folder->relPath), 'folder '.$folder.' can not be found anymore in db');
