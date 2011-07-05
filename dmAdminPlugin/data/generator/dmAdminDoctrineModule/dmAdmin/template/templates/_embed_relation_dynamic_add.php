[?php

$embed_form = $form[$name][$i];
$embed_relation_dynamic = $form->getEmbedRelationDynamic($name);
$relation_name = $embed_relation_dynamic['relationName'];
$object = $form->getObject()->get($relation_name)->get($i);

echo _open('div.element');

  echo £('span.s16.s16_delete.delete_embed_dynamic', __('Delete'));

  echo _open('ul.fields');

    foreach ($embed_form as $key => $field)
    {  
      /* You can add a dm_media representation for a field
      
      if ($name == 'embed_form_name' && $key=='field_name')
      {
        include_partial('reference/embed_relation_dynamic_add_media', array('name' => $name, 'object' => $object->getDmMedia(), 'field' => $field, 'i' => $i));
      }
      else*/
      if ($field instanceof dmFormField)
      {
        if ($field->isHidden())
        {
          $field_html = $field->error()->field();
        }
        else
        {
          $field_html = $field->label()->error()->field();
        }
      }
      else
      {
        $field_html = $field->render();
      }
      
      echo _tag('li.field', $field_html);
    }

  echo _close('ul');

echo £c('div');

?]