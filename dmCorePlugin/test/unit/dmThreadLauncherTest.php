<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test(5);

$cliFile = 'cache/thread_launcher_test.php';
$helper->get('service_container')->mergeParameter('thread_launcher.options', array(
  'cli_file' => $cliFile
));

$threadLauncher = $helper->get('thread_launcher');

$t->is($threadLauncher->getCliFileFullPath(), dmProject::rootify($cliFile), 'cli file is '.dmProject::rootify($cliFile));

$t->ok(file_exists(dmProject::rootify($cliFile)), 'cli file has been created in '.$cliFile);

$threadClass = 'dmThreadTest';
$proofFileName = dmString::random();

try
{
  $threadLauncher->execute(dmString::random());
  $t->fail('thread launcher throws an exception when thread class does not exists');
}
catch(dmException $e)
{
  $t->pass('thread launcher throws an exception when thread class does not exists');
}

$t->diag($threadLauncher->getCommand($threadClass, array('proof_file_name' => $proofFileName)));

try
{
  $threadLauncher->execute($threadClass, array('proof_file_name' => $proofFileName));
  $t->pass('thread launcher executed correctly the thread '.$threadClass);
}
catch(dmException $e)
{
  $t->fail('thread launcher executed correctly the thread '.$threadClass);
  $t->diag('got : '.$threadLauncher->getLastExec('output'));
}

$t->ok(file_exists(dmOs::join(sfConfig::get('sf_cache_dir'), $proofFileName)), 'The thread did its job successfully');