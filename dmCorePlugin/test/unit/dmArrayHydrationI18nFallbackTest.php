<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test(3);

$user = $helper->get('user');
$defaultCulture = sfConfig::get('sf_default_culture');

$user->setCulture($defaultCulture);

$widget = dmDb::create('DmWidget', array(
  'module' => 'dmWidgetContent',
  'action' => 'title',
  'dm_zone_id' => dmDb::table('DmZone')->findOne()->id,
  'value'  => $defaultCulture.' value'
))->saveGet();

$widgetId = $widget->id;

$wanted = array_merge($widget->toArray());

/*
 * Add fancy translations
 */
foreach(array('z1', 'c2', 'c3') as $culture)
{
  $user->setCulture($culture);
  $widget->value = $culture.' value';
  $widget->save();
}
$user->setCulture($defaultCulture);

$widgetArray = dmDb::query('DmWidget w')
->leftJoin('w.Translation t WITH t.lang = ?', $defaultCulture)
->where('w.id = ?', $widgetId)
->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);

$t->is($widgetArray, $wanted, 'Fetched widget array for existing culture');

$query = dmDb::query('DmWidget w')
->leftJoin('w.Translation t WITH t.lang = ? OR t.lang = ?', array('__', $defaultCulture))
->where('w.id = ?', $widgetId);

$t->diag($query->getSqlQuery());

$widgetArray = $query->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);

$t->is($widgetArray, $wanted, 'Fetched widget array with i18n fallback for non existing culture');

$widgetArray = dmDb::query('DmWidget w')
->leftJoin('w.Translation t WITH t.lang = ? OR t.lang = ?', array('z1', $defaultCulture))
->where('w.id = ?', $widgetId)
->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);

$wanted2 = $wanted;
$wanted2['Translation']['z1'] = array(
  'id' => $widget->id,
  'value' => 'z1 value',
  'lang' => 'z1'
);

$t->is($widgetArray, $wanted2, 'Fetched widget array for existing, non-default culture');

//$wanted3 = $wanted;
//$wanted3['value'] = $wanted['Translation'][$defaultCulture]['value'];
//unset($wanted3['Translation']);
//
//$widgetArray = dmDb::query('DmWidget w')
//->leftJoin('w.Translation t WITH t.lang = ? OR t.lang = ?', array('__', $defaultCulture))
//->select('w.*, t.value as value')
//->where('w.id = ?', $widgetId)
//->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);
//
//$t->is($widgetArray, $wanted3, 'Fetched widget array with i18n fallback for non existing culture and mapped value');

$widget->delete();