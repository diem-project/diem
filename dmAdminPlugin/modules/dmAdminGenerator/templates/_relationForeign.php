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
$options          = array_merge(array(
  'new'           => true,
  'sort'          => true
), isset($options) ? $options : array());
/*
 * One to one relations give only one object instead of a collection
 * transform it into an array
 */
if ($foreignRecords instanceof dmDoctrineRecord)
{
  $foreignRecords = array($foreignRecords);
}
$nbforeignRecords = count($foreignRecords);

$hasRoute = $sf_context->getRouting()->hasRouteName($foreignModule->getUnderscore());

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
        $hasRoute
        ? £link($foreignRecord)
        ->text($foreignRecord->__toString())
        ->title(__('Open'))
        ->set('.associated_record.s16right.s16_arrow_up_right_medium')
        : £('span.associated_record', $foreignRecord->__toString())
      );
    }

    echo £c('ul');
  }
  
  if($hasRoute)
  {
    echo £o('ul.actions');
    
    if($options['new'])
    {
      $newLink = £link('@'.$foreignModule->getUnderscore().'?action=new')
      ->text(__('New'))
      ->set('.s16.s16_add_little');
      
      if ($relation instanceof Doctrine_Relation_ForeignKey)
      {
        $newLink->param('defaults['.$relation->getForeign().']', $record->get('id'));
      }
      
      echo £('li', $newLink);
    }
    
    if($options['sort'] && $foreignModule->getTable()->isSortable() && count($foreignRecords) > 1)
    {
      $sortLink = £link(array(
        'sf_route'      => $module->getUnderscore(),
        'id'            => $record->get('id'),
        'action'        => 'sortReferers',
        'refererModule' => $foreignModule->getKey()
      ))->text(__('Sort'))->set('.s16.s16_right_little');
      
      echo £('li', $sortLink);
    }
    
    echo £c('ul');
  }

echo £c('div');