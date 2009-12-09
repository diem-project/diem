<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test(6);

$settingTable = dmDb::table('DmSetting');
$settingTranslationTable = dmDb::table('DmSettingTranslation');

$t->diag('create new setting');
$setting = dmDb::create('DmSetting', array(
  'name' => 'test_'.dmString::random(),
  'description' => 'This is just a test setting',
  'value' => 'fr value',
  'default_value' => 'fr default',
  'type' => 'text',
  'group_name' => 'test group'
));

$t->diag('testing toArray keys');
$t->is(
  array_keys($setting->toArray(false)),
  $settingTable->getFieldNames(),
  'DmSetting->toArray : '.implode(', ', $settingTable->getFieldNames())
);

$t->diag('testing toArrayWithI18n keys');
$expected = array_values(array_unique(array_merge($settingTable->getFieldNames(), $settingTranslationTable->getFieldNames())));
$t->is(
  array_keys($setting->toArrayWithI18n(false)),
  $expected,
  'DmSetting->toArrayWithI18n : '.implode(', ', $expected)
);

$t->diag('testing toArrayWithI18n values');
$expected = array_merge($setting->toArray(false), $setting->getCurrentTranslation()->toArray(false));
ksort($expected);
$result = $setting->toArrayWithI18n(false);
ksort($result);
$t->is(
  $result,
  $expected,
  'DmSetting->toArrayWithI18n : '.implode(', ', $expected)
);

$setting->save();

$t->diag('testing toArrayWithI18n fallback values');

$expected = array_merge($setting->toArray(false), $setting->getCurrentTranslation()->toArray(false));
ksort($expected);

$helper->get('user')->setCulture('c1');

$result = $setting->toArrayWithI18n(false);
ksort($result);
$t->is(
  $result,
  $expected,
  'DmSetting->toArrayWithI18n : '.implode(', ', $expected)
);

$setting->delete();

$t->diag('fetching a setting');

$helper->get('user')->setCulture(sfConfig::get('sf_default_culture'));

$setting = dmDb::query('DmSetting s')->withI18n()->fetchOne();

$t->diag('testing toArrayWithI18n values');
$expected = array_merge($setting->toArray(false), $setting->getCurrentTranslation()->toArray(false));
ksort($expected);
$result = $setting->toArrayWithI18n(false);
ksort($result);
$t->is(
  $result,
  $expected,
  'DmSetting->toArrayWithI18n : '.implode(', ', $expected)
);

$t->diag('testing toArrayWithI18n fallback values');

$expected = array_merge($setting->toArray(false), $setting->getCurrentTranslation()->toArray(false));
ksort($expected);

$helper->get('user')->setCulture('c1');

$result = $setting->toArrayWithI18n(false);
ksort($result);
$t->is(
  $result,
  $expected,
  'DmSetting->toArrayWithI18n : '.implode(', ', $expected)
);