<?php

require_once(realpath(dirname(__FILE__).'/../../..').'/unit/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test(23);

$table = dmDb::table('DmTestPost');

foreach(array(
  // i18n, link, markdown
  'id' => array(false, false, false),
  'title' => array(true, false, false),
  'url' => array(true, true, false),
  'body' => array(true, false, true)
) as $field => $properties)
{
  $t->is($table->isI18nColumn($field), $properties[0], sprintf('Is %s an i18n column: %s', $field, $properties[0] ? 'TRUE' : 'FALSE'));
  $t->is($table->isLinkColumn($field), $properties[1], sprintf('Is %s a link column: %s', $field, $properties[1] ? 'TRUE' : 'FALSE'));
  $t->is($table->isMarkdownColumn($field), $properties[2], sprintf('Is %s a markdown column: %s', $field, $properties[2] ? 'TRUE' : 'FALSE'));
}

$t->diag('Table interaction with page tree');

foreach(array(
  'dmPage' => false,
  'dmUser' => true,
  'dmPermission' => false,
  'dmWidget' => false,
  'dmTransUnit' => false,
  'dmTestComment' => true,
  'dmTestDomain' => true,
  'dmTestCateg' => true,
  'dmTestPost' => true,
  'dmTestTag' => true,
  'dmTestFruit' => true
) as $moduleKey => $interactsWithPageTree)
{
  $t->is(dmDb::table($moduleKey)->interactsWithPageTree(), $interactsWithPageTree, $moduleKey.'->interactsWithPageTree() : '.$interactsWithPageTree);
}