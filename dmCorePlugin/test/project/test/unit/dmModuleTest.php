<?php

require_once(realpath(dirname(__FILE__).'/../../..').'/unit/helper/dmModuleUnitTestHelper.php');
$helper = new dmModuleUnitTestHelper();
$helper->boot();

$moduleManager = $helper->get('module_manager');

$t = new lime_test(83);

$t->comment('Is module test');

foreach(array(
  'dmPage' => true,
  'dmTransUnit' => true,
  'dmWidget' => true,
  'dmWidget' => true,
  'dmTestPost' => true,
  'dmTestDomain' => true,
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
  'dmTestDomain' => true,
  'main' => true,
  'dmTestTag' => true
) as $moduleKey => $isProject)
{
  $t->is($moduleManager->getModule($moduleKey)->isProject(), $isProject, $moduleKey.'->isProject() : '.$isProject);
}

$t->comment('Module components tests');

foreach(array(
  'main' => 'header footer sitemap',
  'dmUser' => 'signin form list show',
  'dmTestCateg' => 'list listByDomain show',
  'dmTestPost' => 'listByDomain listByCateg listByTag show',
  'dmTestComment' => 'listByDomain listByCateg listByPost form'
) as $moduleKey => $componentKeys)
{
  $module = $helper->getModule($moduleKey);

  foreach(explode(' ', $componentKeys) as $componentKey)
  {
    $component = $module->getComponent($componentKey);

    $t->isa_ok($component, 'dmModuleComponent', $moduleKey.'/'.$componentKey.' is a dmModuleComponent');
  }
}

$t->diag('Ancestor tests');

foreach(array(
  'dmTestFruit dmTestPost' => false,
  'dmTestFruit dmTestFruit' => false,
  'dmTestPost dmTestCateg' => true,
  'dmTestPost dmTestDomain' => true,
  'dmTestComment dmTestPost' => true,
  'dmTestComment dmTestCateg' => true,
  'dmTestComment dmTestDomain' => true,
  'dmTestComment dmTestComment' => false,
  'dmTestCateg dmTestComment' => false,
  'dmTestCateg dmTestDomain' => true,
  'dmTestTag dmTestTag' => false,
  'dmTestTag dmTestDomain' => false,
  'dmTestTag dmTestComment' => false,
  'dmTestTag dmUser' => false,
  'dmUser dmTestTag' => false
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
  'dmTestPost dmTestDomain' => false,
  'dmTestComment dmTestPost' => true,
  'dmTestComment dmTestCateg' => false,
  'dmTestComment dmTestComment' => false,
  'dmTestCateg dmTestComment' => false,
  'dmTestCateg dmTestDomain' => true,
  'dmTestTag dmTestTag' => false,
  'dmTestTag dmTestComment' => false
) as $modules => $hasAncestor)
{
  $modules = explode(' ', $modules);
  $t->is($helper->hasNearestAncestorWithPage($modules[0], $modules[1]), $hasAncestor, sprintf('%s has %s nearest ancestor with page: %s',
    $modules[0], $modules[1], $hasAncestor ? 'TRUE' : 'FALSE'
  ));
}

$t->diag('Path tests');

foreach(array(
  'dmTestFruit' => array(),
  'dmTestTag' => array(),
  'dmUser' => array(),
  'dmTestDomain' => array(),
  'dmTestCateg' => array('dmTestDomain'),
  'dmTestPost' => array('dmTestDomain', 'dmTestCateg'),
  'dmTestComment' => array('dmTestDomain', 'dmTestCateg', 'dmTestPost')
) as $module => $path)
{
  $t->is($helper->getPathKeys($module), $path, sprintf('%s path keys = %s', $module, implode(', ', $path)));
}

$t->diag('Path tests including module');

foreach(array(
  'dmTestFruit' => array('dmTestFruit'),
  'dmTestTag' => array('dmTestTag'),
  'dmUser' => array('dmUser'),
  'dmTestDomain' => array('dmTestDomain'),
  'dmTestCateg' => array('dmTestDomain', 'dmTestCateg'),
  'dmTestPost' => array('dmTestDomain', 'dmTestCateg', 'dmTestPost'),
  'dmTestComment' => array('dmTestDomain', 'dmTestCateg', 'dmTestPost', 'dmTestComment')
) as $module => $path)
{
  $t->is($helper->getPathKeys($module, true), $path, sprintf('%s path keys = %s', $module, implode(', ', $path)));
}

$t->diag('Farthest ancestor tests');

foreach(array(
  'dmTestFruit' => null,
  'dmTestTag' => null,
  'dmTestDomain' => null,
  'dmUser' => null,
  'dmTestCateg' => 'dmTestDomain',
  'dmTestPost' => 'dmTestDomain',
  'dmTestComment' => 'dmTestDomain'
) as $module => $farthestAncestor)
{
  $t->is((string)$helper->getFarthestAncestor($module), (string)$farthestAncestor, sprintf('%s farthest ancestor = %s', $module, $farthestAncestor));
}