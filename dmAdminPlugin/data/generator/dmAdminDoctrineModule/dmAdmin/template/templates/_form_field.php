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
      [?php echo $form[$name]->renderLabel($label) ?]

      [?php echo $form[$name]->render($attributes instanceof sfOutputEscaper ? $attributes->getRawValue() : $attributes) ?]

      [?php if ($help || $help = $form[$name]->renderHelp()): ?]
        <div class="help">[?php echo __($help) ?]</div>
      [?php endif; ?]
    </div>
  </div>
[?php else: ?]
  <div class="[?php echo $divClass ?]">
    [?php echo dm_admin_form_field($name, $form); ?]
  </div>
[?php endif; ?]