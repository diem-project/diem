<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test();

$table = dmDb::table('DmLayout');

$t->isa_ok(
  $layout = $table->findFirstOrCreate(),
  'DmLayout',
  'fetched the first layout'
);

$t->ok($layout->exists(), 'the first layout exists');

$t->comment('Delete all layouts');
foreach($table->findAll() as $l)
{
  $l->delete();
}

$t->isa_ok(
  $layout = $table->findFirstOrCreate(),
  'DmLayout',
  'fetched the first layout'
);

$t->ok($layout->exists(), 'the first layout exists');

$areaTop = $layout->getArea('top');
$areaLeft = $layout->getArea('left');
$areaBottom = $layout->getArea('bottom');
$areaOther = $layout->getArea('other');

dmDb::create('DmWidget', array(
  'module' => 'dmWidgetContent',
  'action' => 'title',
  'dm_zone_id' => $areaTop['Zones'][0]->id,
  'value'  => 'widget value 1'
))->save();

dmDb::create('DmWidget', array(
  'module' => 'dmWidgetContent',
  'action' => 'link',
  'dm_zone_id' => $areaTop['Zones'][0]->id,
  'value'  => 'widget value 2'
))->save();

$newZone = dmDb::create('DmZone', array(
  'dm_area_id' => $layout->getArea('top')->id
))->saveGet();

dmDb::create('DmWidget', array(
  'module' => 'dmWidgetContent',
  'action' => 'image',
  'dm_zone_id' => $newZone->id,
  'value'  => 'widget value 3'
))->save();

dm_test_this_layout($layout, $t);

$t->comment('Duplicate layout');
$duplicatedLayout = $layout->duplicate();

$t->is($duplicatedLayout->name, $layout->name.' copy', 'duplicated layout name is '.$duplicatedLayout->name);

$t->comment('Save duplicated layout');
$duplicatedLayout->save();

dm_test_this_layout($duplicatedLayout, $t);

$t->comment('free duplicated layout');
$duplicatedLayout->free(true);
unset($duplicatedLayout);
$duplicatedLayout = $table->findOneByName($layout->name.' copy');

dm_test_this_layout($duplicatedLayout, $t);

$t->comment('Delete duplicated layout');
$duplicatedLayout->delete();

function dm_test_this_layout(DmLayout $layout, lime_test $t)
{
  $layout->refresh(true);
  $areaTop = $layout->getArea('top');
  $areaLeft = $layout->getArea('left');
  $areaBottom = $layout->getArea('bottom');
  $areaOther = $layout->getArea('other');
  $areaTop->refresh(true);
  
  $t->is($layout->Areas->count(), 4, 'Layout has 4 Areas');
  
  $t->is($areaTop['Zones']->count(), 2, 'area top has 2 Zones');
  
  $t->is($areaTop['Zones'][0]->Widgets->count(), 2, 'area top Zones[0] has 2 Widgets');
  
  $t->is($areaTop['Zones'][1]->Widgets->count(), 1, 'area Zones[1] has 1 Widgets');
  
  $t->is($areaLeft->Zones->count(), 1, 'Layout->Areas[1] has 1 Zone');
  
  $t->is($areaLeft->Zones[0]->Widgets->count(), 0, 'Layout->Areas[1]->Zones[0] has 0 Widgets');
  
  $t->is($areaBottom->Zones->count(), 1, 'Layout->Areas[2] has 1 Zone');
  
  $t->is($areaBottom->Zones[0]->Widgets->count(), 0, 'Layout->Areas[2]->Zones[0] has 0 Widgets');
  
  $t->is($areaOther->Zones->count(), 1, 'Layout->Areas[3] has 1 Zone');
  
  $t->is($areaOther->Zones[0]->Widgets->count(), 0, 'Layout->Areas[3]->Zones[0] has 0 Widgets');
  
  $t->is($areaTop['Zones'][0]->Widgets[0]->getModuleAction(), 'dmWidgetContent/title', 'found first widget');
  
  $t->ok($areaTop['Zones'][0]->Widgets[0]->getCurrentTranslation()->exists(), 'first widget has a current translation');
  
  $t->is($areaTop['Zones'][0]->Widgets[0]->value, 'widget value 1', 'first widget value is "widget value 1"');
  
  $t->is($areaTop['Zones'][0]->Widgets[1]->getModuleAction(), 'dmWidgetContent/link', 'found second widget');
  
  $t->is($areaTop['Zones'][0]->Widgets[1]->value, 'widget value 2', 'second widget value is "widget value 2"');
  
  $t->is($areaTop['Zones'][1]->Widgets[0]->getModuleAction(), 'dmWidgetContent/image', 'found third widget');
  
  $t->is($areaTop['Zones'][1]->Widgets[0]->value, 'widget value 3', 'third widget value is "widget value 3"');
}