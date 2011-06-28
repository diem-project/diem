<fieldset id="sf_fieldset_[?php echo preg_replace('/[^a-z0-9_]+/', '_', dmString::transliterate(strtolower($fieldset))) ?]">
  [?php if ('NONE' != $fieldset): ?]
    <h2 class="fieldset_title ui-accordion-header ui-helper-reset ui-state-default ui-corner-top">
      <span class="ui-icon ui-icon-triangle-1-e"></span>
      <span class="fieldset_name">[?php echo __($fieldset) ?]</span>
    </h2>
    <div class="fieldset_content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active">
  [?php else: ?]
    <div class="fieldset_none fieldset_content ui-widget-content ui-corner-all ui-accordion-content-active">
  [?php endif; ?]

  <div class="fieldset_content_inner clearfix">

  [?php $it = 0; foreach ($fields as $name => $field): ?]
    [?php if ((isset($form[$name]) && $form[$name]->isHidden()) || (!isset($form[$name]) && $field->isReal())) continue ?]
    [?php include_partial('<?php echo $this->getModuleName() ?>/form_field', array(
      'name'       => $name,
      'attributes' => $field->getConfig('attributes', array()),
      'label'      => $field->getConfig('label'),
      'help'       => $field->getConfig('help'),
      'form'       => $form,
      'field'      => $field,
      'class'      => ($it%2 ? 'odd' : 'even').' sf_admin_form_row sf_admin_'.strtolower($field->getType()).' sf_admin_form_field_'.$name.' '.(isset($form[$name]) ? dmString::underscore(get_class($form[$name]->getWidget())) : ''),
    ));if ($it%2) { echo '<div style="clear: both"></div>'; } $it++; ?]
  [?php endforeach; ?]

  </div>
  </div>

</fieldset>