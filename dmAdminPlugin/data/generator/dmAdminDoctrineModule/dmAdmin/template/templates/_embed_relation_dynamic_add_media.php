[?php

echo £o('div.dm_media');

  echo £('div.sf_admin_form_row',
         array('data-field-name' => $name.'_'.$i.'_form'),
         $field->render());

  echo £('div.sf_admin_form_row.sf_admin_form_field_'.$name.'_'.$i.'_view',
     get_partial('dmMedia/viewBig', array('object' => $object)));

  echo £('span.clearfix');

echo £c('div');

?]