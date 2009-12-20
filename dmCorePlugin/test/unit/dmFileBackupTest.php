<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test(10);

sfConfig::set('dm_backup_enabled', true);

$backup = $helper->get('file_backup');

$rootDir = sfConfig::get('sf_root_dir');
$backupDir = $backup->getDir();

$t->is($backupDir, dmOs::join(sfConfig::get('sf_data_dir'), 'dm/backup/filesystem'), $backupDir);

$t->ok(is_dir($backupDir), $backupDir.' exists');

$t->ok(is_writable($backupDir), $backupDir.' is writable');

$t->diag('Change backup dir to cache/backupTest');

$backup->setDir('cache/backupTest');

$t->ok(is_dir($backupDir), $backupDir.' exists');

$t->ok(is_writable($backupDir), $backupDir.' is writable');

$t->diag('clear backup');
$backup->clear();

$t->is($backup->getFiles(), array(), 'backup is empty');

$file = dmProject::rootify('config/ProjectConfiguration.class.php');

$t->diag('backup '.$file);

$backup->save($file);

$backupFile = dmOs::join($backup->getDir(), 'config/ProjectConfiguration.class.php.'.date('Y-m-d_H-i-s'));

$t->is(count($backup->getFiles()), 1, 'backup has one file');

$t->is($backup->getFiles(), array($backupFile), 'backup has '.$backupFile);

$t->is(file_get_contents($file), file_get_contents($backupFile), 'backup file is same as file');

$t->diag('clear backup');
$backup->clear();

$t->is($backup->getFiles(), array(), 'backup is empty');