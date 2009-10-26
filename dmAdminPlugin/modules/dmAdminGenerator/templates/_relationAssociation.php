<?php

$moduleManager        = dmContext::getInstance()->getModuleManager();
$module               = $moduleManager->getModuleByModel($record->getTable()->getComponentName());
$relation             = $module->getTable()->getRelation($alias);
$associationModule    = $moduleManager->getModuleByModel($relation->getClass());
$associationRecords   = $record->get($alias);
$nbassociationRecords = count($associationRecords);

echo £o('div.dm_associations');

  echo £o('ul.list');

  foreach($associationRecords as $associationRecord)
  {
    echo £('li',
      £link($associationRecord)
      ->text($associationRecord->__toString())
      ->title(__('Open'))
      ->set('.associated_record.s16right.s16_arrow_up_right_medium')
    );
  }

  echo £c('ul');

echo £c('div');