<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test(15);

$record = new DmMailTemplate;
$model = 'DmMailTemplate';
$table = dmDb::table($model);

$t->ok($table->isVersionable(), $model.' is versionable');

$t->is($record->version, 0, 'new record, version is 0');

$record->name = dmString::random();
$record->description = 'jefferson';
$record->save();

$t->is($record->version, 1, 'saved record, version is 1');
$t->is($record->description, 'jefferson', 'saved record, description is jefferson');

$record->description = 'airplane';
$record->save();

$t->is($record->version, 2, 'saved record, version is 2');
$t->is($record->description, 'airplane', 'saved record, description is airplane');

$record->revert(1);

$t->is($record->version, 1, 'reverted but not saved, record version is 1');
$t->is($record->description, 'jefferson', 'reverted but not saved, record description is jefferson');

$record->save();

$t->is($record->version, 3, 'reverted and saved, record version is 3');
$t->is($record->description, 'jefferson', 'reverted and saved, record description is jefferson');

$t->is(count($record->getCurrentTranslation()->Version), 3, 'record has 3 versions');

$record->description = 'jethro';
$record->save();

$t->is($record->version, 4, 'saved record, version is 4');
$t->is($record->description, 'jethro', 'saved record, description is jethro');

// now with another culture
$id = $record->id;
$record->free(true);
unset($record);
$record = $table->findOne($id);

$helper->get('user')->setCulture('c1');

$record->description = 'c1 description';

$record->save();

$t->is($record->version, 1, 'saved record, version is 1');
$t->is($record->description, 'c1 description', 'saved record, description is c1 description');

$record->delete();