<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test(30);

$settingTable = dmDb::table('DmSetting');

$t->ok($settingTable->hasRelation('Translation'), 'Setting table has relation Translation');

$t->ok($settingTable->getRelationHolder()->has('Translation'), 'Setting table relation holder has Translation');

$t->ok($settingTable->hasI18n(), 'Setting table has I18n');

$user = $helper->get('user');
$user->setCulture('fr');

sfConfig::set('sf_default_culture', 'fr');

$helper->get('i18n')->setCultures(array('fr', 'en', 'es'));

$t->diag(sprintf('default culture : %s, current culture : %s', sfConfig::get('sf_default_culture'), $user->getCulture()));

dmDb::query('DmSetting s')->where('s.name = ?', 'test')->delete()->execute();

$t->diag('create new setting');
$setting = dmDb::create('DmSetting', array(
  'name' => 'test',
  'description' => 'This is just a test setting',
  'value' => 'fr value',
  'default_value' => 'fr default',
  'type' => 'text',
  'group_name' => 'test group'
));

$t->ok($setting->isNew(), 'setting is new');
$t->is($setting->name, 'test', 'name is test');
$t->is($setting->value, 'fr value', 'fr value is fr value');

$user->setCulture('en');

$t->is($setting->name, 'test', 'name is still test');
$t->is($setting->value, 'fr value', 'en value is fr value');

$user->setCulture('fr');

$t->diag('save setting');
$setting->save();

$t->is($setting->value, 'fr value', 'saved fr value is fr value');

$user->setCulture('en');

$t->is($setting->value, 'fr value', 'saved en value is fr value');

$user->setCulture('fr');

$setting->value = 'new fr value';

$t->is($setting->value, 'new fr value', 'new fr value is new fr value');

$user->setCulture('en');

$t->is($setting->value, 'new fr value', 'new en value is new fr value');

$user->setCulture('fr');

$t->is($setting->value, 'new fr value', 'new fr value is new fr value');

$t->is($setting->defaultValue, 'fr default', 'default fr value is fr default');

$user->setCulture('en');

$setting->value = 'new en value';

$t->is($setting->value, 'new en value', 'new en value is new en value');

$t->is($setting->defaultValue, 'fr default', 'default en value is fr default');

$user->setCulture('fr');

$t->is($setting->value, 'new fr value', 'fr value is new fr value');

$user->setCulture('es');

$t->is($setting->value, 'new fr value', 'es value is new fr value');

$setting->delete();

unset($setting);

$t->diag('create new setting');
$setting = dmDb::create('DmSetting', array(
  'name' => 'test',
  'description' => 'Eso es un test',
  'value' => 'es value',
  'default_value' => 'es default',
  'type' => 'text',
  'group_name' => 'test group'
));

$t->is($setting->value, 'es value', 'es value is es value');

$user->setCulture('fr');

$t->is($setting->value, null, 'fr value is null');

$setting->save();

$t->is($setting->value, null, 'fr value is null');

$user->setCulture('es');

$t->is($setting->value, 'es value', 'es value is es value');

$user->setCulture('en');

$t->is($setting->value, null, 'en value is null');

$user->setCulture('fr');

$setting->value = 'fr value';

$t->is($setting->value, 'fr value', 'fr value is fr value');

$user->setCulture('en');

$t->is($setting->value, 'fr value', 'en value is fr value');

$setting->value = 'en value';

$t->is($setting->value, 'en value', 'en value is en value');

$user->setCulture('es');

$t->is($setting->value, 'es value', 'es value is es value');

$setting->save();

$setting->free(true);

unset($setting);

$setting = dmDb::query('DmSetting s')->where('s.name = ?', 'test')->fetchOne();

$user->setCulture('fr');

$setting->value = 'fr value';

$t->is($setting->value, 'fr value', 'fr value is fr value');

$user->setCulture('en');

$t->is($setting->value, 'en value', 'en value is en value');

$user->setCulture('es');

$t->is($setting->value, 'es value', 'es value is es value');

$setting->delete();