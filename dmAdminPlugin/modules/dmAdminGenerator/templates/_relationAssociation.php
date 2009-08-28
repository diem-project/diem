<?php

$module               = dmModuleManager::getModuleByModel($record->getTable()->getComponentName());
$relation             = $module->getTable()->getRelation($alias);
$associationModule    = dmModuleManager::getModuleByModel($relation->getClass());
$associationRecords   = $record->get($alias);
$nbassociationRecords = count($associationRecords);

echo £o('div.dm_associations');

  echo £o('ul.list');

  foreach($associationRecords as $associationRecord)
  {
    echo £('li',
      dmAdminLinkTag::build(array(
        'sf_route' => $associationModule->getUnderscore().'_edit',
        'sf_subject' => $associationRecord
      ))->text($associationRecord)
    );
  }

  echo £c('ul');

echo £c('div');