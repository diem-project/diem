<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot('front');

$modules = $helper->getModuleManager()->getModules();

$t = new lime_test(count(dmProject::getAllModels()));

$t->diag('Table interaction with page tree');

foreach(dmProject::getAllModels() as $model)
{
  $table = dmDb::table($model);
  
  if ($table instanceof dmDoctrineTable)
  {
    if($module = $table->getDmModule())
    {
      $interactsWithPageTree = $module->isProject();
    }
    else
    {
      $interactsWithPageTree = false;
      foreach($table->getRelationHolder()->getLocals() as $localRelation)
      {
        if ($localModule = $helper->get('module_manager')->getModuleByModel($localRelation['class']))
        {
          if ($localModule->interactsWithPageTree())
          {
            $interactsWithPageTree = true;
            break;
          }
        }
      }
    }
  
    $t->is($table->interactsWithPageTree(), $interactsWithPageTree, get_class($table).'->interactsWithPageTree() : '.($interactsWithPageTree ? 'YES' : 'NO'));
  }
}