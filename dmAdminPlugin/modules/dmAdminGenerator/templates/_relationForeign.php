<?php

$moduleManager    = dmContext::getInstance()->getModuleManager();
$module           = $moduleManager->getModuleByModel($record->getTable()->getComponentName());

if(!$module)
{
  throw new dmException(sprintf('no module found for model %s', $record->getTable()->getComponentName()));
}

$relation         = $module->getTable()->getRelation($alias);
$foreignModule    = $moduleManager->getModuleByModel($relation->getClass());
$foreignRecords   = $record->get($alias);
/*
 * One to one relations give only one object instead of a collection
 * transform it into an array
 */
if ($foreignRecords instanceof myDoctrineRecord)
{
  $foreignRecords = array($foreignRecords);
}

$nbforeignRecords = count($foreignRecords);

echo £o('div.dm_foreigns');

  if ($nbforeignRecords)
  {
    echo £o('ul.list');

    foreach($foreignRecords as $foreignRecord)
    {
      echo £('li',
        dmAdminLinkTag::build(array(
          'sf_route' => $foreignModule->getUnderscore().'_edit',
          'sf_subject' => $foreignRecord
        ))->text($foreignRecord)
      );
    }

    echo £c('ul');
  }
  
  $newLink = dmAdminLinkTag::build('@'.$foreignModule->getUnderscore().'_new')
  ->text(__('New'))
  ->set('.s16.s16_add_little');
  
  if ($relation instanceof Doctrine_Relation_ForeignKey)
  {
    $newLink->param('defaults['.$relation->getForeign().']', $record->id);
  }

  echo £('ul.actions',
    £('li', $newLink).
    (($foreignModule->getTable()->isSortable() && count($foreignRecords) > 1)
    ? £('li', dmAdminLinkTag::build(array(
      'sf_route'      => $module->getUnderscore().'_do',
      'id'            => $record->get('id'),
      'action'        => 'sortReferers',
      'refererModule' => $foreignModule->getKey()
    ))->text(__('Sort'))->set('.s16.s16_right_little'))
    : '')
  );

echo £c('div');