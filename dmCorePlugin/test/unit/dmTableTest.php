<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test(5);

$model = 'DmMailTemplate';
$translationModel = $model.'Translation';
new $model;
$table = dmDb::table($model);
$i18nTable = dmDb::table($translationModel);

$i18nTable->setColumnOption('body', 'extra', 'markdown');

$t->is($i18nTable->isMarkdownColumn('body'), true, $translationModel.'.body is a markdown column');

$t->is($table->isMarkdownColumn('body'), true, $model.'.body is a markdown column');


try
{
  $table->testWeirdAttribute;
  $t->pass('table->testWeirdAttribute does not exist');
}
catch(Doctrine_Table_Exception $e)
{
  $t->fail('table->testWeirdAttribute does not exist');
}

try
{
  $table->testWeirdMethod();
  $t->fail('table->testWeirdMethod() does not exist');
}
catch(Doctrine_Table_Exception $e)
{
  $t->pass('table->testWeirdMethod() does not exist');
}

try
{
  $table->getTestWeirdMethod();
  $t->fail('table->getTestWeirdMethod() does not exist');
}
catch(Doctrine_Table_Exception $e)
{
  $t->pass('table->getTestWeirdMethod() does not exist');
}