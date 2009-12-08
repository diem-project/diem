<?php

require_once(dirname(__FILE__).'/helper/dmTestHelper.php');
$helper = new dmTestHelper();
$helper->boot();

$t = new lime_test(2);

$model = 'DmMailTemplate';
$translationModel = $model.'Translation';
new $model;
$table = dmDb::table($model);
$i18nTable = dmDb::table($translationModel);

$i18nTable->setColumnOption('body', 'extra', 'markdown');

$t->is($i18nTable->isMarkdownColumn('body'), true, $translationModel.'.body is a markdown column');

$t->is($table->isMarkdownColumn('body'), true, $model.'.body is a markdown column');