[?php
$alias = dmString::camelize(substr($field, 0, strlen($field)-5));
?]
<div class="dm_form_pagination {field: '[?php echo $field?]', link: '[?php echo $link?]', selection:  [[?php echo $form->getDefault($alias) ?] ], currentPage: [?php echo $pager->getPage()?], lastPage: [?php echo $pager->getLastPage()?]}">

[?php // MAX PER PAGE

$maxPerPages = array();
$message = __('elements per page');
foreach(sfConfig::get('dm_admin_max_per_page', array(10)) as $maxPerPage)
{
  $maxPerPages[$maxPerPage] = $maxPerPage.' '.$message;
}
$currentMaxPerPage = $sf_user->getAttribute(
  '<?php echo $this->getModuleName() ?>.' . $field . '.max_per_page',
  <?php echo isset($this->config['list']['max_per_page']) ? (integer) $this->config['list']['max_per_page'] : 10 ?>,
  'admin_module'
);
$maxPerPageSelect = new sfWidgetFormSelect(array('choices' => $maxPerPages));
echo $maxPerPageSelect->render('dm_max_per_page', $currentMaxPerPage, array(
  'id' => $field . '_max_per_page',
  'class' => 'dm_max_per_page',
  'disabled' => $pager->getNbResults() === 0
));
?]

[?php if ($pager->getPage() > 1): ?]
[?php if ($pager->getPage() > 2): ?]
  <a title="[?php echo __('First page'); ?]" href="[?php echo url_for1('<?php echo $this->getUrlForAction('list') ?>') ?]?page=1">
    <span class="s16block s16_first">&lt;&lt;</span>
  </a>
[?php else: ?]
    <div class="disabled"><span class="s16block s16_first"></span></div>
[?php endif; ?]
  
  <a title="[?php echo __('Previous page'); ?]" href="[?php echo url_for1('<?php echo $this->getUrlForAction('list') ?>') ?]?page=[?php echo ($pager->getPage() - 1) ?]">
    <span class="s16block s16_previous">&lt;</span>
  </a>
[?php else: ?]
    <div class="disabled"><span class="s16block s16_first"></span></div>
    <div class="disabled"><span class="s16block s16_previous"></span></div>
[?php endif; ?]
  
  [?php echo _tag('div.dm_pagination_status.fleft', preg_replace('|(\d+)|', '<strong>$1</strong>', __('%1% - %2% of %3%',
    array(
      '%1%' => $pager->getNbResults() ? $pager->getFirstIndice() : 0,
      '%2%' => $pager->getLastIndice(),
      '%3%' => $pager->getNbResults()
    ))));
  ?]

[?php if ($pager->getPage() < $pager->getLastPage()): ?]
  <a title="[?php echo __('Next page'); ?]" href="[?php echo url_for1('<?php echo $this->getUrlForAction('list') ?>') ?]?page=[?php echo ($pager->getPage() + 1) ?]">
    <span class="s16block s16_next">&nbsp;</span>
  </a>

[?php if ($pager->getPage() < ($pager->getLastPage()-1)): ?]
  <a title="[?php echo __('Last page'); ?]" href="[?php echo url_for1('<?php echo $this->getUrlForAction('list') ?>') ?]?page=[?php echo $pager->getLastPage() ?]">
    <span class="s16block s16_last">&nbsp;</span>
  </a>
[?php else: ?]
    <div class="disabled"><span class="s16block s16_next"></span></div>
[?php endif; ?]
[?php else: ?]
    <div class="disabled"><span class="s16block s16_next"></span></div>
    <div class="disabled"><span class="s16block s16_last"></span></div>
[?php endif; ?]
</div>