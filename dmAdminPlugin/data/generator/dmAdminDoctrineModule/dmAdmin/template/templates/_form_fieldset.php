[?php $it = 0; $totalFields = count($fields); ?]
<fieldset id="sf_fieldset_[?php echo preg_replace('/[^a-z0-9_]+/', '_', dmString::transliterate(strtolower($fieldset))) ?]" [?php if ($totalFields==1):?] class="single" [?php endif; ?]>
  [?php if ('NONE' != $fieldset): ?]
    <h2 class="fieldset_title ui-accordion-header ui-helper-reset ui-state-default ui-corner-top">
      <span class="ui-icon ui-icon-triangle-1-e"></span>
      <span class="fieldset_name">[?php echo __($fieldset, array(), '<?php echo $this->getModule()->getOption('i18n_catalogue')?>') ?]</span>
    </h2>
    <div class="fieldset_content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active">
  [?php else: ?]
    <div class="fieldset_none fieldset_content ui-widget-content ui-corner-all ui-accordion-content-active">
  [?php endif; ?]

  <div class="fieldset_content_inner clearfix">

  [?php foreach ($fields as $name => $field): ?]
    [?php if ((isset($form[$name]) && $form[$name]->isHidden()) || (!isset($form[$name]) && $field->isReal())) continue ?]
    [?php
      $extraClasses=array();
      $extraClasses[] = $it%2 ? 'even' : 'odd';
      if ($totalFields!=1) {
        if ($it==0) {  $extraClasses[]='first'; }
        if ($it==$totalFields-1) {  $extraClasses[]='last'; }
      }
      include_partial('<?php echo $this->getModuleName() ?>/form_field', array(
      'helper'     => $helper,
      'name'       => $name,
      'attributes' => $field->getConfig('attributes', array()),
      'label'      => $field->getConfig('label'),
      'help'       => $field->getConfig('help'),
      'form'       => $form,
      'field'      => $field,
      'search' => isset($search) ? $search : null,
      'class'      => implode($extraClasses,' ').' sf_admin_form_row sf_admin_'.strtolower($field->getType()).' sf_admin_form_field_'.$name.' '.(isset($form[$name]) ? dmString::underscore(get_class($form[$name]->getWidget())) : ''),
    ));if ($it%2) { echo '<div style="clear: both"></div>'; } $it++; ?]
  [?php endforeach; ?]

  </div>
  </div>

</fieldset>