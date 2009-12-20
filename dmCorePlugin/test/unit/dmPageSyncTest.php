<?php

require_once(dirname(__FILE__).'/helper/dmPageUnitTestHelper.php');
$helper = new dmPageUnitTestHelper();
$helper->boot();

$showModules = $helper->getModuleManager()->getModulesWithPage();

$helper->get('page_tree_watcher')->connect();

$nbBigIterations = 1;

$t = new lime_test();

$t->diag('page sync tests');

$helper->clearDatabase($t);
$helper->loremizeDatabase(3, $t);

$pageTable = dmDb::table('DmPage');

$helper->clearPages($t); // 7 tests

$helper->testNewPage($t); // 5 tests

$helper->testNestedTree($t); // 8 tests

$helper->testI18nFetching($t); // 6 tests

$helper->syncPages($t); // 1 test

$helper->checkTreeIntegrity($t); // 2 tests

$t->diag('Randomly add 3 or more records by table, and add associations');

$helper->loremizeDatabase(6, $t);

$helper->syncPages($t); // 1 test

$helper->checkTreeIntegrity($t); // 2 tests

$pageTreeWatcher = $helper->get('page_tree_watcher');

for($it = 1; $it<=$nbBigIterations; $it++)
{
	$t->diag('Randomly delete 3 records by table');

	foreach($helper->getModuleManager()->getModulesWithPage() as $module)
	{
		foreach($module->getTable()->createQuery()->limit(3)->fetchRecords() as $record)
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

	$t->diag('Randomly add 3 or more records by table');

	$helper->loremizeDatabase(6, $t);

  $helper->updatePageTreeWatcher($t);
//	$helper->syncPages($t); // 1 test

	$helper->checkTreeIntegrity($t); // 2 tests

	$t->diag('Randomly update 3 records by table');

	foreach($helper->getModuleManager()->getModulesWithModel() as $module)
	{
		foreach($module->getTable()->createQuery('r')->select('r.*, RANDOM() as rand')->orderBy('rand')->limit(3)->fetchRecords() as $record)
		{
			$oldRecord = clone $record;
			try
			{
		    dmRecordLoremizer::loremize($record, true);
//        dmDebug::show('modified '.get_class($record).' '.$record->id, $record->getModified(), $record->toArray(), Doctrine_Lib::getRecordStateAsString($record->state()));
		    $record->save();
			}
			catch(Exception $e)
			{
				dmDebug::show($oldRecord->id, $oldRecord, $record->id, $record);
				throw new dmException('Error when loremizing '.$module.' record '.$record->id.' : '.get_class($e).' : '.$e->getMessage());
			}
		}
	}

  $helper->updatePageTreeWatcher($t);
//	$helper->syncPages($t); // 1 test

	$helper->checkTreeIntegrity($t); // 2 tests
}

$helper->testI18nFetching($t); // 6 tests