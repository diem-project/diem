<div class="dm_module_search">
  [?php
    $currentSearch = $sf_user->getAppliedSearchOnModule('<?php echo $this->getModuleName(); ?>');
    printf('<form action="%s" method="get">', url_for1(array('sf_route' => '<?php echo $this->getModule()->getUnderscore(); ?>')));
    printf('<input id="dm_module_search_input" type="text" title="%s" value="%s" name="search"/>',
      __('Search in %1%', array('%1%' => '<?php echo $this->getModule()->getPlural(); ?>')),
      $currentSearch
    );
    if ($currentSearch)
    {
      printf('<a href="%s" class="s16 s16_cross" title="%s">&nbsp;</a>', url_for1(array('sf_route' => '<?php echo $this->getModule()->getUnderscore(); ?>')).'?search=', __('Cancel search'));
    }
    printf('<input type="submit" class="dm_submit" value="%s" />', __('Search'));
  ?]
  </form>
  
</div>