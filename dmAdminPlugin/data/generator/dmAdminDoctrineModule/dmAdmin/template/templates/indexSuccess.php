[?php
  use_helper('I18N', 'Date');
  use_stylesheet('admin.list');

  $appliedFilters = $sf_user->getAppliedFiltersOnModule('<?php echo $this->getModuleName(); ?>');
?]

<div id="sf_admin_container" class='{baseUrl: "[?php echo url_for('<?php echo $this->getUrlForAction('list') ?>') ?]"}'>
  <div id="list_header" class="clearfix">
    <h1>[?php echo <?php echo $this->getI18NString('list.title') ?> ?]</h1>
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

  <div id="sf_admin_content" [?php if (!$pager->getNbResults()) echo 'class="no_results"'; ?]>
<?php if ($this->configuration->getValue('list.batch_actions')): ?>
    <form action="[?php echo url_for('<?php echo $this->getUrlForAction('do') ?>', array('action' => 'batch')) ?]" method="post">
<?php endif; ?>
    [?php include_partial('<?php echo $this->getModuleName() ?>/list', array('pager' => $pager, 'sort' => $sort, 'helper' => $helper)) ?]
<?php if ($this->configuration->getValue('list.batch_actions')): ?>
    </form>
<?php endif; ?>
  </div>

  <div id="sf_admin_footer">
    [?php include_partial('<?php echo $this->getModuleName() ?>/list_footer', array('pager' => $pager)) ?]
  </div>
</div>
