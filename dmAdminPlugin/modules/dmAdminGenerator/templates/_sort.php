<?php use_helper('Form');

use_stylesheet('admin.sort');
use_javascript('lib.ui-sortable');
use_javascript('admin.sort');

$submit =
£('div.text_align_right',
  £('span.info', __('Drag & drop elements, then')).
  submit_tag(__('Save'))
);

echo £o('div.dm_sort.dm_box.big');

  echo £('h1.title', __('Sort %1%', array('%1%' => $module->getPlural())));

  echo £o('div.dm_box_inner');

  echo form_tag('dmAdminGenerator/saveSort');

  echo input_hidden_tag('dm_module', $module->getKey());

  echo £('div.fleft', £link('@'.$module->getUnderscore())->text('&laquo; '.__('Back to list')));

  echo $submit;

  echo £o('ol.objects');

  foreach($objects as $object)
  {
    echo £('li.object',
      $object.
      input_hidden_tag('dm_sort_element['.$object->id.']', true)
    );
  }

  echo £c('ol');

  echo $submit;

  echo '</form>';

  echo £c('div');

echo £c('div');