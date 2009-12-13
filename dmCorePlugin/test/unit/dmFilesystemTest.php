<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test(11);

$fs = $helper->get('filesystem');

$t->comment($command = 'cd ./');

$t->is($fs->exec($command), true, 'Valid exec returns true');

$t->ok(is_array($fs->getLastExec()), '$fs->getLastExec() returns an array');

$t->is($fs->getLastExec('command'), $command, 'last command is '.$command);

$t->comment($badCommand = 'cd diem-test-'.dmString::random(8));

$t->is($fs->exec($badCommand), false, 'Invalid exec returns false');

$t->ok(is_array($fs->getLastExec()), '$fs->getLastExec() returns an array');

$t->is($fs->getLastExec('command'), $badCommand, 'last command is '.$badCommand);

$t->comment($command = 'echo "this a is CLI test"');

$t->is($fs->exec($command), true, 'Valid exec returns true');

$t->ok(is_array($fs->getLastExec()), '$fs->getLastExec() returns an array');

$t->is($fs->getLastExec('command'), $command, 'last command is '.$command);

$t->is($fs->getLastExec('output'), 'this a is CLI test'."\n", 'Output is "'.$fs->getLastExec('output').'"');

$t->comment('Unix command : '.($command = 'whoami'));

$t->is($fs->execute($command), $success = ('/' === DIRECTORY_SEPARATOR), 'Execution : '.$success);