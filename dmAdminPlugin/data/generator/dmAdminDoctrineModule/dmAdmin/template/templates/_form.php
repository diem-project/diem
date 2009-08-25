[?php include_stylesheets_for_form($form) ?]
[?php include_javascripts_for_form($form) ?]

<div class="sf_admin_form">

  [?php $form_actions =
    get_partial('<?php echo $this->getModuleName() ?>/form_pagination', array('<?php echo $this->getSingularName() ?>' => $<?php echo $this->getSingularName() ?>, 'form' => $form, 'configuration' => $configuration, 'helper' => $helper, 'nearRecords' => $nearRecords))
    .
    get_partial('<?php echo $this->getModuleName() ?>/form_actions', array('<?php echo $this->getSingularName() ?>' => $<?php echo $this->getSingularName() ?>, 'form' => $form, 'configuration' => $configuration, 'helper' => $helper))
    ;
  ?]


  [?php echo form_tag_for($form, '@<?php echo $this->params['route_prefix'] ?>') ?]

  <div class="dm_form_actions dm_form_actions_top clearfix">
    [?php echo $form_actions; ?]
  </div>

    <div class="sf_admin_form_inner ui-widget ui-accordion">

    [?php echo $form->renderHiddenFields() ?]

    [?php if ($form->hasGlobalErrors()): ?]
      [?php echo $form->renderGlobalErrors() ?]
    [?php endif; ?]

    [?php foreach ($configuration->getFormFields($form, $form->isNew() ? 'new' : 'edit') as $fieldset => $fields): ?]
      [?php include_partial('<?php echo $this->getModuleName() ?>/form_fieldset', array('<?php echo $this->getSingularName() ?>' => $<?php echo $this->getSingularName() ?>, 'form' => $form, 'fields' => $fields, 'fieldset' => $fieldset)) ?]
    [?php endforeach; ?]

    </div>

  <div class="dm_form_actions dm_form_actions_bottom">
    [?php echo $form_actions; ?]
  </div>

  </form>

</div>