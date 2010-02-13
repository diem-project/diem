<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test(15);

$fs = $helper->get('filesystem');

$t->comment($command = 'cd ./');

$t->is($fs->exec($command), true, 'Valid exec returns true');

$t->ok(is_array($fs->getLastExec()), '$fs->getLastExec() returns an array');

$t->is($fs->getLastExec('command'), $command, 'last command is '.$command);

$t->comment($badCommand = 'cd diem-test-'.dmString::random(8));

$t->is($fs->exec($badCommand), false, 'Invalid exec returns false');

$t->ok(is_array($fs->getLastExec()), '$fs->getLastExec() returns an array');

$t->is($fs->getLastExec('command'), $badCommand, 'last command is '.$badCommand);

$t->comment($command = 'echo diem-test');

$t->is($fs->exec($command), true, 'Valid exec returns true');

$t->ok(is_array($fs->getLastExec()), '$fs->getLastExec() returns an array');

$t->is($fs->getLastExec('command'), $command, 'last command is '.$command);

$t->is($helper->fixLinebreaks($fs->getLastExec('output')), 'diem-test'."\n", 'Output is "'.$fs->getLastExec('output').'"');

$t->comment('Unix command : '.($command = 'whoami'));

$t->is($fs->exec($command), $success = ('/' === DIRECTORY_SEPARATOR), 'Execution : '.$success);

$t->comment('Test ->getFilesInDir()');

$fs->mirror(dirname(__FILE__), sfConfig::get('sf_cache_dir'), sfFinder::type('file'));
mkdir(sfConfig::get('sf_cache_dir').'/test_dir');
$fullFind = $fs->find('file')->maxdepth(0)->in(sfConfig::get('sf_cache_dir'));
$fastFind = $fs->findFilesInDir(sfConfig::get('sf_cache_dir'));

$t->ok(count($fullFind) > 50, 'sfFinder finds '.count($fullFind).' files');

$t->ok(in_array($file = sfConfig::get('sf_cache_dir').'/'.basename(__FILE__), $fullFind), 'sfFinder found '.$file);

$t->is(count($fastFind), count($fullFind), '->findFilesInDir finds '.count($fullFind).' files');

$t->is_deeply($fastFind, $fullFind, '->findFilesInDir() works like sfFinder');