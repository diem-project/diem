<div class="dm_form_pagination">

[?php if ($pager->getPage() > 1): ?]
[?php if ($pager->getPage() > 2): ?]
  <a title="[?php echo __('First page'); ?]" href="[?php echo url_for('<?php echo $this->getUrlForAction('list') ?>') ?]?page=1">
    <span class="s16block s16_first">&lt;&lt;</span>
  </a>
[?php endif; ?]
  
  <a title="[?php echo __('Previous page'); ?]" href="[?php echo url_for('<?php echo $this->getUrlForAction('list') ?>') ?]?page=[?php echo ($pager->getPage() - 1) ?]">
    <span class="s16block s16_previous">&lt;</span>
  </a>
[?php endif; ?]
  
  [?php echo £('div.dm_pagination_status.fleft', preg_replace('|(\d+)|', '<strong>$1</strong>', __('%1% - %2% on %3%',
    array(
      '%1%' => $pager->getNbResults() ? $pager->getFirstIndice() : 0,
      '%2%' => $pager->getLastIndice(),
      '%3%' => $pager->getNbResults()
    ))));
  ?]

[?php if ($pager->getPage() < $pager->getLastPage()): ?]
  <a title="[?php echo __('Next page'); ?]" href="[?php echo url_for('<?php echo $this->getUrlForAction('list') ?>') ?]?page=[?php echo ($pager->getPage() + 1) ?]">
    <span class="s16block s16_next">&nbsp;</span>
  </a>

[?php if ($pager->getPage() < ($pager->getLastPage()-1)): ?]
  <a title="[?php echo __('Last page'); ?]" href="[?php echo url_for('<?php echo $this->getUrlForAction('list') ?>') ?]?page=[?php echo $pager->getLastPage() ?]">
    <span class="s16block s16_last">&nbsp;</span>
  </a>
[?php endif; ?]
[?php endif; ?]
</div>