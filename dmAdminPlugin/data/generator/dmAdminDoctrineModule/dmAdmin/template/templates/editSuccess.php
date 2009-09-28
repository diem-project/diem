[?php 
  use_helper('Date');
  use_stylesheet('admin.form');
  use_javascript('core.form');
  use_javascript('admin.form');
  use_javascript('lib.ui-resizable');
?]

<div id="sf_admin_container">

  <div class="dm_search_bar clearfix">
  [?php include_partial('<?php echo $this->getModuleName() ?>/search');?]
  </div>

[?php if ($form->hasErrors()): ?]
  <ul class="dm_error_list">
    [?php foreach ($form as $field => $widget): ?]
      [?php if($error = dmArray::first($widget->getError())): ?]
        <li>
          <a href="#<?php echo $this->getModule()->getUnderscore() ?>_[?php echo $field ?]" class="link">
            [?php echo $field." : ".$error ?]
          </a>
        </li>
      [?php endif; ?]
    [?php endforeach; ?]
  </ul>
[?php endif; ?]

  <div id="sf_admin_header">
    [?php include_partial('<?php echo $this->getModuleName() ?>/form_header', array('<?php echo $this->getSingularName() ?>' => $<?php echo $this->getSingularName() ?>, 'form' => $form, 'configuration' => $configuration)) ?]
  </div>

  <div id="sf_admin_content">
    [?php include_partial('<?php echo $this->getModuleName() ?>/form', array('<?php echo $this->getSingularName() ?>' => $<?php echo $this->getSingularName() ?>, 'form' => $form, 'configuration' => $configuration, 'helper' => $helper, 'nearRecords' => $nearRecords)) ?]
  </div>

  <div id="sf_admin_footer">
    [?php include_partial('<?php echo $this->getModuleName() ?>/form_footer', array('<?php echo $this->getSingularName() ?>' => $<?php echo $this->getSingularName() ?>, 'form' => $form, 'configuration' => $configuration)) ?]
  </div>
</div>
