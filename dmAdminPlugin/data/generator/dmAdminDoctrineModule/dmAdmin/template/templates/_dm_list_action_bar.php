[?php // BATCH ACTIONS ?]
  
[?php include_partial('<?php echo $this->getModuleName() ?>/list_batch_actions', array('helper' => $helper)) ?]

[?php // LIENS  ?]

[?php include_partial('<?php echo $this->getModuleName() ?>/list_actions', array('helper' => $helper)) ?]

[?php //PAGINATION ?]

[?php include_partial('<?php echo $this->getModuleName() ?>/list_pagination', array('helper' => $helper, 'pager' => $pager)) ?]