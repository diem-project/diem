[?php
  $required = ($validator = $form->getValidatorSchema()->offsetGet($name)) ? $validator->getOption('required') : false;
  $divClass = dmArray::toHtmlCssClasses(array($class, $field->isBig() ? 'big' : '', $required ? 'required' : ''));
?]
[?php if ($field->isPartial()): ?]
  <div class="[?php echo $divClass ?]">[?php include_partial('<?php echo $this->getModuleName() ?>/'.$name, array('<?php echo $this->getModuleName() ?>' => $form->getObject(), 'form' => $form, 'attributes' => $attributes instanceof sfOutputEscaper ? $attributes->getRawValue() : $attributes)) ?]</div>
[?php elseif ($field->isComponent()): ?]
  <div class="[?php echo $divClass ?]">[?php include_component('<?php echo $this->getModuleName() ?>', $name, array('<?php echo $this->getModuleName() ?>' => $form->getObject(), 'form' => $form, 'attributes' => $attributes instanceof sfOutputEscaper ? $attributes->getRawValue() : $attributes)) ?]</div>
[?php elseif ($field->isMarkdown()): ?]
  [?php include_partial("dmAdminGenerator/markdown", array("form" => $form, "field" => $field, "class" => $class, "name" => $name, "label" => $label, "attributes" => $attributes, "help" => $help)); ?]
[?php elseif(isset($form[$name])): ?]
  <div class="[?php echo $divClass ?][?php $form[$name]->hasError() and print ' errors' ?]">
    [?php if ($form[$name]->hasError()): ?]
      <div class="error">
        <div class="s16 s16_error">[?php echo __((string) $form[$name]->getError()) ?]</div>
      </div>
    [?php endif; ?]
    <div class="sf_admin_form_row_inner clearfix">
      [?php
      
      echo '<div class="label_wrap">';
      
      echo $form[$name]->renderLabel($label);
      
      if($form[$name]->getWidget() instanceof sfWidgetFormDmDoctrineChoiceMany)
      {
        echo sprintf('<div class="control selection"><span class="select_all">%s</span><span class="unselect_all">%s</span></div>', __('Select all'), __('Unselect all'));
      }
      
      echo '</div>';

      echo '<div class="content">'.$form[$name]->render($attributes instanceof sfOutputEscaper ? $attributes->getRawValue() : $attributes).'</div>';

      if ($help)
      {
        echo '<div class="help">'.__($help).'</div>';
      }
      elseif($help = $form[$name]->renderHelp())
      {
        echo '<div class="help">'.$help.'</div>';
      }
      ?]
    </div>
  </div>
[?php else: ?]
  <div class="[?php echo $divClass ?]">
    [?php echo dm_admin_form_field($name, $form); ?]
  </div>
[?php endif; ?]