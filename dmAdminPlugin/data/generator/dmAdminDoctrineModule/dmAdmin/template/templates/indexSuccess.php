[?php
  use_helper('I18N', 'Date');
  use_stylesheet('admin.list');
  
  $appliedFilters = $sf_user->getAppliedFiltersOnModule('<?php echo $this->getModuleName(); ?>');

  slot('dm.mini_search_form');
  include_partial('<?php echo $this->getModuleName() ?>/search');
  end_slot();
?]

<div id="sf_admin_container" class='{baseUrl: "[?php echo url_for('<?php echo $this->getUrlForAction('list') ?>') ?]"}'>

  <div id="list_header" class="clearfix">
    [?php
      if (count($appliedFilters))
      {
        echo £o('div.s16.s16_magnifier.dm_active_search');
        //echo __('Search').' : ';
        $appliedFiltersHtml = array();
        foreach($appliedFilters as $appliedFilter)
        {
          $appliedFiltersHtml[] =
            __($configuration->getFormFilterField($filters, $appliedFilter)->getConfig('label', dmString::humanize($appliedFilter))).
            ' = '.
            dmArray::get($filters->getDefault($appliedFilter), 'text', __('yes'));
        }
        echo implode(', ', $appliedFiltersHtml);
        echo link_to(__('Back to list'), '<?php echo $this->getUrlForAction('list') ?>', array('action' => 'filter'), array('query_string' => '_reset', 'method' => 'post', 'class' => 'ml10 reset'));
        echo £c('div');
      }
    ?]
  </div>

  <div id="sf_admin_header">
    [?php include_partial('<?php echo $this->getModuleName() ?>/list_header', array('pager' => $pager)) ?]
  </div>

  [?php $dmListActionBar = get_partial('<?php echo $this->getModuleName() ?>/list_action_bar', array('pager' => $pager, 'helper' => $helper, 'class' => 'dm_pagination_top')); ?]
  
  <div id="sf_admin_content" [?php if (!$pager->getNbResults()) echo 'class="no_results"'; ?]>
    
    <form action="[?php echo url_for($helper->getRouteArrayForAction('batch')) ?]" method="post">
    
    <div class="dm_list_action_bar dm_list_action_bar_top clearfix">[?php echo str_replace('__DM_RANDOM_ID__', dmString::random(8), $dmListActionBar); ?]</div>
    
    [?php include_partial('<?php echo $this->getModuleName() ?>/list', array('pager' => $pager, 'sort' => $sort, 'helper' => $helper)) ?]
    
    <div class="dm_list_action_bar dm_list_action_bar_bottom clearfix">[?php echo str_replace('__DM_RANDOM_ID__', dmString::random(8), $dmListActionBar); ?]</div>
    
    </form>
    
    <div class="dm_list_global_actions clearfix">

      [?php if($sf_user->can('export_table')): ?]
      <div class="dm_export">
      <?php echo $this->getLinkToAction('Export CSV', array('action' => 'export', 'params' => array('class' => 'dm_sort s16 s16_export')), false); ?>
      </div>
      [?php endif; ?]
    
      [?php if($configuration->getLoremize() && $sf_user->can('loremize')): ?]
      <div class="dm_loremize">
      <p class="dm_sort s16 s16_edit fleft">Loremize :</p>
      [?php
      $loremizeLink = £link('@<?php echo $this->getModule()->getUnderscore() ?>?action=loremize&nb=__DM_NB_RECORDS__')
      ->text('__DM_NB_RECORDS__')
      ->set('.ml10')
      ->render();
      foreach(array(1, 5, 10, 20, 50, 100) as $nbRecords)
      {
        echo str_replace('__DM_NB_RECORDS__', $nbRecords, $loremizeLink);
      }
      ?]
      </div>
      [?php endif; ?]
      
    </div>
    
  </div>

  <div id="sf_admin_footer">
    [?php include_partial('<?php echo $this->getModuleName() ?>/list_footer', array('pager' => $pager)) ?]
  </div>
</div>