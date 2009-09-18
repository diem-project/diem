[?php include_partial('<?php echo $this->getModuleName() ?>/list_actions', array('helper' => $helper)) ?]

<div>
  [?php if ($pager->getNbResults()) include_partial('<?php echo $this->getModuleName() ?>/pagination', array('pager' => $pager)) ?]
</div>

<div class="dm_module_search">
  [?php
    echo sprintf('<input class="%s" type="text" title="%s" value="%s" name="%s" disabled="disabled"/>',
      'hint dm_module_search_input',
      __('Search'),
      $sf_user->getAppliedSearchOnModule('<?php echo $this->getModuleName(); ?>'),
      "dm_module_search"
    );
  ?]
</div>