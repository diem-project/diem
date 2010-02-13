[?php use_stylesheets_for_form($form) ?]
[?php use_javascripts_for_form($form) ?] 
<div class="sf_admin_filter">

  <div class="sf_admin_filter_inner">

  [?php if ($form->hasGlobalErrors()): ?]
    [?php echo $form->renderGlobalErrors() ?]
  [?php endif; ?]

  <form action="[?php echo url_for('<?php echo $this->getUrlForAction('collection') ?>', array('action' => 'filter')) ?]" method="post">

        [?php foreach ($configuration->getFormFilterFields($form) as $name => $field): ?]
        [?php if ((isset($form[$name]) && $form[$name]->isHidden()) || (!isset($form[$name]) && $field->isReal())) continue ?]
          [?php include_partial('<?php echo $this->getModuleName() ?>/filters_field', array(
            'name'       => $name,
            'attributes' => $field->getConfig('attributes', array()),
            'label'      => $field->getConfig('label'),
            'help'       => $field->getConfig('help'),
            'form'       => $form,
            'field'      => $field,
            'class'      => 'sf_admin_form_row sf_admin_'.strtolower($field->getType()).' sf_admin_filter_field_'.$name.(in_array($name, $appliedFilters) ? ' active' : ''.toggle(' even'))
          )) ?]
        [?php endforeach; ?]

    [?php echo $form->renderHiddenFields() ?]
    <div class="dm_filter_actions">
      [?php echo link_to(__('Reset'), '<?php echo $this->getUrlForAction('collection') ?>', array('action' => 'filter', 'class' => 'reset'), array('query_string' => '_reset', 'method' => 'post')) ?]
      <input type="submit" value="[?php echo __('Filter') ?]" />
    </div>
  </form>

  </div>
</div>