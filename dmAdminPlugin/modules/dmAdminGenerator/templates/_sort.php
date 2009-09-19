<?php

use_stylesheet('admin.sort');
use_javascript('lib.ui-sortable');
use_javascript('admin.sort');

$submit =
£('div.text_align_right',
  £('span.info', __('Drag & drop elements, then')).
  $form->renderSubmitTag(__('Save modifications'))
);

//echo $form->renderGlobalErrors();

echo £o('div.dm_sort.dm_box.big');

  echo £('h1.title', __('Sort %1%', array('%1%' => $form->getModule()->getPlural())));

  echo £o('div.dm_box_inner');

    echo $form->open();
  
    echo £('div.fleft', £link('@'.$module->getUnderscore())->text('&laquo; '.__('Back to list')));
  
    echo $submit;
  
    echo £o('ol.objects');
  
    foreach($form->getRecords() as $record)
    {
      $fieldName = $record->get('id');
      
      echo £('li.object', $form[$fieldName]->renderLabel().$form[$fieldName]->render());
    }
  
    echo £c('ol');
  
    echo $submit;
  
    echo '</form>';

  echo £c('div');

echo £c('div');