<?php

use_stylesheet('admin.sort');
use_javascript('lib.ui-sortable');
use_javascript('admin.sort');

$submit =
_tag('div.text_align_right',
  _tag('span.info', __('Drag & drop elements, then')).
  $form->renderSubmitTag(__('Save modifications'))
);

//echo $form->renderGlobalErrors();

echo _open('div.dm_sort.dm_box.big');

  echo _tag('h1.title', __('Sort %1% for %2%', array('%1%' => $form->getModule()->getPlural(), '%2%' => $form->getParentRecord())));

  echo _open('div.dm_box_inner');

    echo $form->open();
  
    echo _tag('div.fleft', _link('@'.$form->getParentRecord()->getDmModule()->getUnderscore())->text('&laquo; '.__('Back to list')));
  
    echo $submit;
  
    echo _open('ol.objects');
  
    foreach($form->getRecords() as $record)
    {
      $fieldName = $record->get('id');
      
      echo _tag('li.object', $form[$fieldName]->renderLabel().$form[$fieldName]->render());
    }
  
    echo _close('ol');
  
    echo $submit;
  
    echo '</form>';

  echo _close('div');

echo _close('div');