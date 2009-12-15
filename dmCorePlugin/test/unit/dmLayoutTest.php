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

dmDb::create('DmWidget', array(
  'module' => 'dmWidgetContent',
  'action' => 'title',
  'dm_zone_id' => $layout->Areas[0]->Zones[0]->id,
  'value'  => 'widget value 1'
))->save();

dmDb::create('DmWidget', array(
  'module' => 'dmWidgetContent',
  'action' => 'link',
  'dm_zone_id' => $layout->Areas[0]->Zones[0]->id,
  'value'  => 'widget value 2'
))->save();

$newZone = dmDb::create('DmZone', array(
  'dm_area_id' => $layout->Areas[0]->id
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
  $layout->Areas[0]->refresh(true);
  
  $t->is($layout->Areas->count(), 4, 'Layout has 4 Areas');
  
  $t->is($layout->Areas[0]->Zones->count(), 2, 'Layout->Areas[0] has 2 Zones');
  
  $t->is($layout->Areas[0]->Zones[0]->Widgets->count(), 2, 'Layout->Areas[0]->Zones[0] has 2 Widgets');
  
  $t->is($layout->Areas[0]->Zones[1]->Widgets->count(), 1, 'Layout->Areas[0]->Zones[1] has 1 Widgets');
  
  $t->is($layout->Areas[1]->Zones->count(), 1, 'Layout->Areas[1] has 1 Zone');
  
  $t->is($layout->Areas[1]->Zones[0]->Widgets->count(), 0, 'Layout->Areas[1]->Zones[0] has 0 Widgets');
  
  $t->is($layout->Areas[2]->Zones->count(), 1, 'Layout->Areas[2] has 1 Zone');
  
  $t->is($layout->Areas[2]->Zones[0]->Widgets->count(), 0, 'Layout->Areas[2]->Zones[0] has 0 Widgets');
  
  $t->is($layout->Areas[3]->Zones->count(), 1, 'Layout->Areas[3] has 1 Zone');
  
  $t->is($layout->Areas[3]->Zones[0]->Widgets->count(), 0, 'Layout->Areas[3]->Zones[0] has 0 Widgets');
  
  $t->is($layout->Areas[0]->Zones[0]->Widgets[0]->getModuleAction(), 'dmWidgetContent.title', 'found first widget');
  
  $t->ok($layout->Areas[0]->Zones[0]->Widgets[0]->getCurrentTranslation()->exists(), 'first widget has a current translation');
  
  $t->is($layout->Areas[0]->Zones[0]->Widgets[0]->value, 'widget value 1', 'first widget value is "widget value 1"');
  
  $t->is($layout->Areas[0]->Zones[0]->Widgets[1]->getModuleAction(), 'dmWidgetContent.link', 'found second widget');
  
  $t->is($layout->Areas[0]->Zones[0]->Widgets[1]->value, 'widget value 2', 'second widget value is "widget value 2"');
  
  $t->is($layout->Areas[0]->Zones[1]->Widgets[0]->getModuleAction(), 'dmWidgetContent.image', 'found third widget');
  
  $t->is($layout->Areas[0]->Zones[1]->Widgets[0]->value, 'widget value 3', 'third widget value is "widget value 3"');
}