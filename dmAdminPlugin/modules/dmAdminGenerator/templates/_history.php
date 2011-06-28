<?php use_helper('Date');

if (!count($revisions))
{
  echo _tag('h2.mt40.text_align_center', __('No revision for %1% with culture "%2%"', array(
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

echo _open('div.dm_history.mt10');

echo _open('ul.version_tabs');

foreach($revisions as $revision)
{
  echo _tag('li.version_tab', _tag('a href=#revision_'.$revision->version, $revision->version));
}

echo _close('ul');

$model = get_class($record);
$revisionModel = get_class($revision);
$fields = array_diff($revisions[0]->getTable()->getFieldNames(), array('id', 'version', 'lang'));
$recordDiff = $sf_context->get('record_text_diff');

$table = _table();
$table->head(__('Field'), __('Difference'), __('Value'));

$nbRevisions = count($revisions);

foreach($revisions as $index => $revision)
{
  $recordDiff->compare(
    dmArray::get($revisions, $index+1, new $revisionModel()),
    $revision
  );

  $diffs  = $recordDiff->getHtmlDiffs($fields);
  $values = $recordDiff->getHtmlValues($fields);
  
  echo _open('div.revision.dm_data#revision_'.$revision->version);
  
  echo _open('div.revision_header');

  echo _link($record)
  ->text(__('Back to %1%', array('%1%' => $record->__toString())))
  ->set('.s16.s16_arrow_left.back_to_record');

  if($index !== 0)
  {
    echo _link('+/dmAdminGenerator/revert')
    ->textTitle(__('Revert to revision %1%', array('%1%' => $revision->version)))
    ->set('.dm_medium_button.dm_js_confirm.revert_to_revision')
    ->params(array(
      'model'   => $model,
      'pk'      => $record->getPrimaryKey(),
      'version' => $revision->version
    ));
  }
  
  echo _tag('p.revision_title',
    __('Revision %number%', array('%number%' => $revision->version)).
    (isset($values['updated_by'])
    ? ' - '.$values['updated_by']
    : '').
    (isset($values['updated_at'])
    ? ' - '.format_date($values['updated_at'], 'f')
    : '')
  );

  echo _close('div');
  
  $table->clearBody();
  
  foreach($fields as $field)
  {
    $table->body(
      _tag('div', __(dmString::humanize($field))),
      _tag('div', $diffs[$field]),
      _tag('div', $values[$field])
    );
  }
  
  echo $table;
  
  echo _close('div');
}

echo _close('div');