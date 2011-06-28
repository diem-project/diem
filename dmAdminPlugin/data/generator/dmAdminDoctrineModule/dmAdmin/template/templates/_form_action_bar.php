[?php //PAGINATION ?]

[?php include_partial('<?php echo $this->getModuleName() ?>/form_actions', array('form' => $form, 'helper' => $helper, '<?php echo $this->getSingularName() ?>' => $<?php echo $this->getSingularName() ?>)) ?]

[?php include_partial('<?php echo $this->getModuleName() ?>/form_pagination', array('helper' => $helper, '<?php echo $this->getSingularName() ?>' => $<?php echo $this->getSingularName() ?>, 'nearRecords' => $nearRecords)) ?]