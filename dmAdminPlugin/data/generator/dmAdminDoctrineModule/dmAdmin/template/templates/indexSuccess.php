[?php
  use_helper('Date');
  use_stylesheet('admin.filter');
  use_stylesheet('admin.list');
  

  slot('dm.mini_search_form');
  include_partial('<?php echo $this->getModuleName() ?>/search');
  end_slot();
?]

<div id="sf_admin_container" class='{baseUrl: "[?php echo url_for1('<?php echo $this->getUrlForAction('list') ?>') ?]"}'>

  <div id="sf_admin_header">
    [?php include_partial('<?php echo $this->getModuleName() ?>/list_header', array('pager' => $pager, 'helper' => $helper)) ?]
  </div>

  [?php $dmListActionBar = get_partial('<?php echo $this->getModuleName() ?>/list_action_bar', array('pager' => $pager, 'helper' => $helper, 'class' => 'dm_pagination_top', 'configuration' => $configuration)); ?]
  
  <div id="sf_admin_content" [?php if (!$pager->getNbResults()) echo 'class="no_results"'; ?]>
    
    <form action="[?php echo url_for($helper->getRouteArrayForAction('batch')) ?]" method="post">
    
    <div class="dm_list_action_bar dm_list_action_bar_top clearfix">[?php echo str_replace('__DM_RANDOM_ID__', dmString::random(8), $dmListActionBar); ?]</div>
    
    [?php include_partial('<?php echo $this->getModuleName() ?>/list', array('pager' => $pager, 'sort' => $sort, 'helper' => $helper, 'security_manager' => $security_manager)) ?]
    
    <div class="dm_list_action_bar dm_list_action_bar_bottom clearfix">[?php echo str_replace('__DM_RANDOM_ID__', dmString::random(8), $dmListActionBar); ?]</div>
    
    </form>
    
    <div class="dm_list_global_actions clearfix">

      [?php if(false && $sf_user->can('export_table')): ?]
      <div class="dm_export">
      <?php echo $this->getLinkToAction('Export CSV', array('action' => 'export', 'params' => array('class' => 'dm_sort s16 s16_export')), false); ?>
      </div>
      [?php endif; ?]
    
      [?php if($configuration->getLoremize() && $sf_user->can('loremize')): ?]
      <div class="dm_loremize">
      <p class="dm_sort s16 s16_edit fleft">[?php echo __('Loremize', array(), '<?php echo $this->getModule()->getOption('i18n_catalogue')?>'); ?]:</p>
      [?php
      $loremizeLink = _link('@<?php echo $this->getModule()->getUnderscore() ?>?action=loremize&nb=__DM_NB_RECORDS__')
      ->text('__DM_NB_RECORDS__')
      ->set('.ml10.dm_js_confirm')
      ->title(__('Generate %1% random %2%', array('%1%' => '__DM_NB_RECORDS__', '%2%' => __('<?php echo addslashes($this->getModule()->getPlural()) ?>', array(), '<?php echo $this->getModule()->getOption('i18n_catalogue')?>'))), '<?php echo $this->getModule()->getOption('i18n_catalogue')?>')
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
    [?php include_partial('<?php echo $this->getModuleName() ?>/list_footer', array('pager' => $pager, 'helper' => $helper)) ?]
  </div>
</div>