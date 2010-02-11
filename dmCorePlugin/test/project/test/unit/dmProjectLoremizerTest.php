<?php

require_once(realpath(dirname(__FILE__).'/../../..').'/unit/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test(66);

$helper->clearDatabase($t);

$loremizer = $helper->get('project_loremizer');

foreach(array(0, 5, 10, 15, 20, 20) as $nb)
{
  $t->comment('Loremizing database with '.$nb.' records per table');
  
  $startTime = microtime(true);
  
  $loremizer->execute($nb);
  
  $elapsedTime = (microtime(true) - $startTime);
  
  $t->comment(sprintf('Completed in %.02f seconds', $elapsedTime));
  
  foreach($helper->get('module_manager')->getProjectModules() as $module)
  {
    if($module->hasModel())
    {
      $t->is($module->getTable()->count(), $nb, $module.' module has '.$nb.' records');
    }
  }

  foreach(array('DmTestDomainCateg', 'DmTestPostTag') as $associationTable)
  {
    $t->is($count = dmDb::query($associationTable.' r')->count(), $nb*2, $associationTable.' association table has '.$count.' records');
  }
}
