<?php

require_once(realpath(dirname(__FILE__).'/../../..').'/unit/helper/dmModuleUnitTestHelper.php');
$helper = new dmModuleUnitTestHelper();
$helper->boot();

$moduleManager = $helper->get('module_manager');

$t = new lime_test(50);

$t->comment('Is module test');

foreach(array(
  'dmPage' => true,
  'dmTransUnit' => true,
  'dmWidget' => true,
  'dmWidget' => true,
  'dmTestPost' => true,
  'main' => true,
  'dmTestTag' => true,
  'dmTestPostTag' => false,
  'dm' => false,
  '' => false,
  ' main ' => false,
) as $moduleKey => $exists)
{
  $t->is($moduleManager->hasModule($moduleKey), $exists, $moduleKey.' exists : '.$exists);
}

$t->diag('isProject tests');

foreach(array(
  'dmPage' => false,
  'dmTransUnit' => false,
  'dmWidget' => false,
  'dmWidget' => false,
  'dmTestPost' => true,
  'main' => true,
  'dmTestTag' => true
) as $moduleKey => $isProject)
{
  $t->is($moduleManager->getModule($moduleKey)->isProject(), $isProject, $moduleKey.'->isProject() : '.$isProject);
}

$t->diag('Ancestor tests');

foreach(array(
  'dmTestFruit dmTestPost' => false,
  'dmTestFruit dmTestFruit' => false,
  'dmTestPost dmTestCateg' => true,
  'dmTestComment dmTestPost' => true,
  'dmTestComment dmTestCateg' => true,
  'dmTestComment dmTestComment' => true,
  'dmTestCateg dmTestComment' => false,
  'dmTestTag dmTestTag' => false,
  'dmTestTag dmTestComment' => false
) as $modules => $hasAncestor)
{
  $modules = explode(' ', $modules);
  $t->is($helper->hasAncestor($modules[0], $modules[1]), $hasAncestor, sprintf('%s has %s ancestor: %s',
    $modules[0], $modules[1], $hasAncestor ? 'TRUE' : 'FALSE'
  ));
}

$t->diag('Nearest ancestor with page tests');

foreach(array(
  'dmTestFruit dmTestPost' => false,
  'dmTestFruit dmTestFruit' => false,
  'dmTestPost dmTestCateg' => true,
  'dmTestComment dmTestPost' => true,
  'dmTestComment dmTestCateg' => true,
  'dmTestComment dmTestComment' => true,
  'dmTestCateg dmTestComment' => false,
  'dmTestTag dmTestTag' => false,
  'dmTestTag dmTestComment' => false
) as $modules => $hasAncestor)
{
  $modules = explode(' ', $modules);
  $t->is($helper->hasAncestor($modules[0], $modules[1]), $hasAncestor, sprintf('%s has %s nearest ancestor with page: %s',
    $modules[0], $modules[1], $hasAncestor ? 'TRUE' : 'FALSE'
  ));
}

$t->is($helper->hasNearestAncestorWithPage('dmTestFruit', 'info'), false, 'dmTestFruit has no info nearest ancestor with page');
$t->is($helper->hasNearestAncestorWithPage('dmTestFruit', 'dmTestFruit'), false, 'dmTestFruit has no dmTestFruit nearest ancestor with page');
$t->is($helper->hasNearestAncestorWithPage('rubrique', 'domaine'), true, 'rubrique has domaine nearest ancestor with page');
$t->is($helper->hasNearestAncestorWithPage('info', 'rubrique'), true, 'info has rubrique nearest ancestor with page');
$t->is($helper->hasNearestAncestorWithPage('info', 'domaine'), false, 'info has no domaine nearest ancestor with page');
$t->is($helper->hasNearestAncestorWithPage('domaine', 'info'), false, 'domaine has no info nearest ancestor with page');
$t->is($helper->hasNearestAncestorWithPage('info', 'info'), false, 'info has no info nearest ancestor with page');
$t->is($helper->hasNearestAncestorWithPage('feature', 'featureCateg'), false, 'feature has no featureCateg nearest ancestor with page');
$t->is($helper->hasNearestAncestorWithPage('feature', 'featureType'), true, 'feature has featureType nearest ancestor with page');
$t->is($helper->hasNearestAncestorWithPage('role', 'docCateg'), false, 'role has no docCateg nearest ancestor with page');
$t->is($helper->hasNearestAncestorWithPage('role', 'docType'), false, 'role has no docType nearest ancestor with page');
$t->is($helper->hasNearestAncestorWithPage('role', 'doc'), true, 'role has doc nearest ancestor with page');

$t->diag('Path tests');

$t->is($helper->getPathKeys('dmTestFruit'), array(), 'dmTestFruit has an empty path');
$t->is($helper->getPathKeys('dmTestFruit', true), array('dmTestFruit'), 'dmTestFruit full path = dmTestFruit');
$t->is($helper->getPathKeys('domaine'), array(), 'domaine has an empty path');
$t->is($helper->getPathKeys('rubrique'), array('domaine'), 'rubrique path = domaine');
$t->is($helper->getPathKeys('rubrique', true), array('domaine', 'rubrique'), 'rubrique full path = domaine, rubrique');
$t->is($helper->getPathKeys('info'), array('domaine', 'rubrique'), 'info path = domaine, rubrique');
$t->is($helper->getPathKeys('info', true), array('domaine', 'rubrique', 'info'), 'info full path = domaine, rubrique, info');
$t->is($helper->getPathKeys('feature', true), array('featureType', 'featureCateg', 'feature'), 'info full path = featureType, featureCateg, feature');

$t->diag('Farthest ancestor tests');

$t->is($helper->getFarthestAncestor('dmTestFruit'), null, 'dmTestFruit has no farthest ancestor');
$t->is($helper->getFarthestAncestor('domaine'), null, 'domaine has no farthest ancestor');
$t->is($helper->getFarthestAncestor('rubrique')->getKey(), 'domaine', 'rubrique farthest ancestor = domaine');
$t->is($helper->getFarthestAncestor('info')->getKey(), 'domaine', 'info farthest ancestor = domaine');
$t->is($helper->getFarthestAncestor('role')->getKey(), 'docType', 'role farthest ancestor = docType');

//$t->diag('Dir tests');
//
//$appModulesDir =  sfConfig::get('sf_app_module_dir');
//$adminModulesDir = dmOs::join(sfConfig::get('dm_admin_dir'), 'modules');
//
//$dir = dmOs::join($appModulesDir, 'dmTestFruit');         $t->is($helper->getDir('dmTestFruit'), $dir, 'dmTestFruit dir : '.$dir);
//$dir = dmOs::join($appModulesDir, 'info');            $t->is($helper->getDir('info'), $dir, 'info dir : '.$dir);
//$dir = dmOs::join($adminModulesDir, 'dmPage');        $t->is($helper->getDir('dmPage'), $dir, 'dmPage dir : '.$dir);
//$dir = null;                                          $t->is($helper->getDir('main'), $dir, 'main dir : '.$dir);