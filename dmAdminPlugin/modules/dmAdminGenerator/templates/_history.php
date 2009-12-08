<?php

if (!count($revisions))
{
  echo £('h2.mt40.text_align_center', __('No revision for %1% with culture "%2%"', array(
    '%1%' => $record,
    '%2%' => $sf_user->getCulture()
  )));
  return;
}

use_javascript('lib.ui-tabs');
use_stylesheet('lib.ui-tabs');

use_javascript('admin.history');
use_stylesheet('admin.history');
use_stylesheet('admin.dataTable');

echo £o('div.dm_history.mt10');

echo £o('ul.version_tabss');

foreach($revisions as $revision)
{
  echo £('li.version_tab', £('a href=#revision_'.$revision->version, $revision->version));
}

echo £c('ul');

$fields = array_diff($revisions[0]->getTable()->getFieldNames(), array('id', 'version', 'lang'));

$table = £table();
$table->head(__('Field'), __('Difference'), __('Value'));

$nbRevisions = count($revisions);

foreach($revisions as $index => $revision)
{
  echo £o('div.revision.dm_data#revision_'.$revision->version);
  
  $revertText = __('Revert to revision %1%', array('%1%' => $revision->version));
  echo £('ul.actions',
    £('li',
      $index !== 0
      ? £link('+/dmAdminGenerator/revert')
      ->text($revertText)
      ->title($revertText)
      ->set('.s16.s16_arrow_curve_180.dm_js_confirm')
      ->params(array(
        'model'   => get_class($record),
        'pk'      => $record->getPrimaryKey(),
        'version' => $revision->version
      ))
      : £('span.s16.s16_arrow_curve_180', $revertText)
    )
  );
  
  $table->clearBody();
  
  if ($index < ($nbRevisions - 1))
  {
    $diffs = $sf_context->getServiceContainer()
    ->setParameter('record_text_diff.from_version', $revisions[$index+1])
    ->setParameter('record_text_diff.to_version', $revision)
    ->getService('record_text_diff')
    ->getHtmlDiffs($fields);
  }
  else
  {
    $diffs = false;
  }
  
  foreach($fields as $field)
  {
    $table->body(
      £('div', __(dmString::humanize($field))),
      £('div', $diffs ? nl2br($diffs[$field]) : '-'),
      £('div', nl2br($revision->get($field)))
    );
  }
  
  echo $table;
  
  echo £c('div');
}

echo £c('div');