<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test(2);

$settingTable = dmDb::table('DmSetting');
$settingTranslationTable = dmDb::table('DmSettingTranslation');
$user = $helper->get('user');
$i18n = $helper->get('i18n');

$i18n->setCultures(array_merge(
  $i18n->getCultures(),
  array('c1', 'c2')
));

$user->setCulture(sfConfig::get('sf_default_culture'));

$t->diag('Clearing settings translations for cultures c1 and c2');

$settingTranslationTable->createQuery('t')->whereIn('t.lang', array('c1', 'c2'))->delete()->execute();

$t->diag('load a setting');
$setting = dmDb::query('DmSetting s')->withI18n()->fetchOne();

$form = new DmSettingForm($setting);

$settingValues = $setting->toArrayWithI18n(false);
unset($settingValues['lang']);
ksort($settingValues);
$formDefaults = $form->getDefaults();
ksort($formDefaults);
$t->is($formDefaults, $settingValues, 'Form defaults contain i18n existing values');

$t->diag('Giving the user a new culture the setting does NOT have');
$user->setCulture('c1');

$form = new DmSettingForm($setting);

$settingValues = $setting->toArrayWithI18n(false);

unset($settingValues['lang']);
ksort($settingValues);
$formDefaults = $form->getDefaults();
ksort($formDefaults);
$t->is($formDefaults, $settingValues, 'Form defaults contain i18n fallback values');