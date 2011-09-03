[?php

use_stylesheet('admin.embedDynamic');

echo _open('div.sf_admin_form_row_inner');

  echo _tag('div.label_wrap', $form[$name]->renderLabel($label));

  echo _open('div.elements');

    foreach($form[$name] as $i => $null)
    {
      include_partial('<?php echo $this->getModuleName() ?>/embed_relation_dynamic_add', array('form' => $form, 'i' => $i, 'field' => $field, 'name' => $name));
    }

  echo _close('div');

  echo _tag('span.s16.s16_add.add_embed_dynamic',__('Add'));

echo _close('div');

?]