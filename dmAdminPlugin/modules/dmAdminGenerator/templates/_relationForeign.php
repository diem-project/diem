<?php

/*
 * Yes, this is a controller and should live in a component.
 * But sometimes a single partial is faster.
 */
if(!$module = $record->getDmModule())
{
  throw new dmException(sprintf('no module found for model %s', get_class($record)));
}

$relation         = $module->getTable()->getRelation($alias);
$foreignModule    = $sf_context->getModuleManager()->getModuleByModel($relation->getClass());
$foreignRecords   = $record->get($alias);
/*
 * One to one relations give only one object instead of a collection
 * transform it into an array
 */
if ($foreignRecords instanceof dmDoctrineRecord)
{
  $foreignRecords = array($foreignRecords);
}
$nbforeignRecords = count($foreignRecords);

/*
 * End of the infamous controller
 */

echo £o('div.dm_foreigns');

  if ($nbforeignRecords)
  {
    echo £o('ul.list');

    foreach($foreignRecords as $foreignRecord)
    {
      echo £('li',
        £link($foreignRecord)
        ->text($foreignRecord->__toString())
        ->title(__('Open'))
        ->set('.associated_record.s16right.s16_arrow_up_right_medium')
      );
    }

    echo £c('ul');
  }
  
  $newLink = £link('@'.$foreignModule->getUnderscore().'?action=new')
  ->text(__('New'))
  ->set('.s16.s16_add_little');
  
  if ($relation instanceof Doctrine_Relation_ForeignKey)
  {
    $newLink->param('defaults['.$relation->getForeign().']', $record->get('id'));
  }

  echo £('ul.actions',
    £('li', $newLink).
    (($foreignModule->getTable()->isSortable() && count($foreignRecords) > 1)
    ? £('li', £link(array(
      'sf_route'      => $module->getUnderscore(),
      'id'            => $record->get('id'),
      'action'        => 'sortReferers',
      'refererModule' => $foreignModule->getKey()
    ))->text(__('Sort'))->set('.s16.s16_right_little'))
    : '')
  );

echo £c('div');