<div class="dm_module_search">
  
  <a class="dm_open_filter_box ui-corner-all s16 s16_gear" title="[?php echo __('Advanced search', array(), 'dm'); ?]">
    [?php
      $nbAppliedFilters = count($sf_user->getAppliedFiltersOnModule('<?php echo $this->getModuleName(); ?>'));
      echo $nbAppliedFilters ? $nbAppliedFilters : '';
    ?]
  </a>
  <div class="dm_filter_box" data-load-url="[?php echo _link('@<?php echo $this->getModule()->getUnderscore() ?>?action=showFilters')->getHref() ?]"></div>
  [?php
    $currentSearch = $sf_user->getAppliedSearchOnModule('<?php echo $this->getModuleName(); ?>');
    printf('<form action="%s" method="get">', url_for1(array('sf_route' => '<?php echo $this->getModule()->getUnderscore(); ?>')));
    printf('<input id="dm_module_search_input" class="ui-corner-left" type="text" title="%s" value="%s" name="search"/>',
      __('Search in %1%', array('%1%' => __("<?php echo $this->getModule()->getPlural(); ?>"), 'dm')),
      $currentSearch
    );
    printf('<input type="submit" class="dm_submit ui-corner-right" value="%s" />', __('Search', array(), 'dm'));
    if ($currentSearch)
    {
      printf('<a href="%s" class="s16block s16_cross dm_cancel_search" title="%s">&nbsp;</a>', url_for1(array('sf_route' => '<?php echo $this->getModule()->getUnderscore(); ?>')).'?search=', __('Cancel search', array(), 'dm'));
    }
  ?]
  </form>
  
</div>