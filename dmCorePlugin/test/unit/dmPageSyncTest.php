<?php

require_once(dirname(__FILE__).'/helper/dmPageUnitTestHelper.php');
$helper = new dmPageUnitTestHelper();
$helper->boot();

$showModules = $helper->getModuleManager()->getModulesWithPage();

$helper->get('page_tree_watcher')->connect();

$nbBigIterations = 2;

$t = new lime_test();

$t->diag('page sync tests');

$helper->clearDatabase($t);
$helper->loremizeDatabase(6, $t);

$pageTable = dmDb::table('DmPage');

$helper->clearPages($t); // 7 tests

$helper->testNewPage($t); // 5 tests

$helper->testNestedTree($t); // 8 tests

$helper->testI18nFetching($t); // 6 tests

$helper->syncPages($t); // 1 test

$helper->checkTreeIntegrity($t); // 2 tests

/*
 * Stop here because of sqlite bug
 */
return;

$t->diag('Randomly add 2 records by table, and add associations');

$helper->loremizeDatabase(6, $t);

$helper->syncPages($t); // 1 test

$helper->checkTreeIntegrity($t); // 2 tests

$pageTreeWatcher = $helper->get('page_tree_watcher');

for($it = 1; $it<=$nbBigIterations; $it++)
{
	$t->diag('Randomly delete 2 records by table');

	foreach($helper->getModuleManager()->getModulesWithPage() as $module)
	{
		foreach($module->getTable()->createQuery()->limit(2)->fetchRecords() as $record)
		{
		  try
		  {
			  $record->delete();
		  }
		  catch(Exception $e)
		  {
		    $t->diag(sprintf('Can not delete %s record : %s', $module, $e->getMessage()));
		  }
		}
	}

	$helper->updatePageTreeWatcher($t);
//	$helper->syncPages($t); // 1 test

	$helper->checkTreeIntegrity($t); // 2 tests

	$t->diag('Randomly add 2 records by table');

	$helper->loremizeDatabase(6, $t);

  $helper->updatePageTreeWatcher($t);
//	$helper->syncPages($t); // 1 test

	$helper->checkTreeIntegrity($t); // 2 tests

	$t->diag('Randomly update 2 records by table');

	$recordLoremizer = $helper->get('record_loremizer')
	->setOption('override', true)
	->setOption('create_associations', true);
	
	foreach($helper->getModuleManager()->getModulesWithModel() as $module)
	{
	  $records = $module->getTable()->createQuery('r')
    ->select('r.*')
    ->withI18n(null, $module->getModel(), 'r')
	  ->addSelect('RANDOM() as rand')
	  ->orderBy('rand')
	  ->limit(2)
	  ->fetchRecords();
	  
		foreach($records as $record)
		{
			$recordLoremizer->execute($record)->save();
		}
	}

  $helper->updatePageTreeWatcher($t);
//	$helper->syncPages($t); // 1 test

	$helper->checkTreeIntegrity($t); // 2 tests
}

$helper->testI18nFetching($t); // 6 tests