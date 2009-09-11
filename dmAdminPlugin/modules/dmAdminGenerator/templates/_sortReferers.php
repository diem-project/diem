<?php use_helper('Form');

use_stylesheet('admin.sort');
use_javascript('admin.sort');

$submit =
£('div.text_align_right',
  £('span.info', __('Drag & drop elements, then')).
  submit_tag(__('Save'), array('class' => 'green'))
);

echo £o('div.dm_sort.dm_box.dm_box.big');

  echo £('h1.title', __('Sort %1% for %2%', array('%1%' => $refererModule->getPlural(), '%2%' => $object)));

  echo £o('div.dm_box_inner');

  echo form_tag('dmGenerator/saveSortReferers');

  echo input_hidden_tag('dm_module', $module->getKey());
  echo input_hidden_tag('dm_referer_module', $refererModule->getKey());

  echo £('div.fleft', £link('@'.$module->getUnderscore())->text('&laquo; '.__('Back to %1% list', array('%1%' => __($module->getPlural())))));

  echo $submit;

  echo £o('ol.objects');

  foreach($refererObjects as $object)
  {
    echo £('li.object',
      $object.
      input_hidden_tag('dm_sort_element['.$object->getId().']', true)
    );
  }

  echo £c('ol');

  echo $submit;

  echo '</form>';

  echo £c('div');

echo £c('div');