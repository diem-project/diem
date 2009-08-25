[?php use_helper('Form'); ?]

<div class="sf_admin_pagination clearfix">

  <div class="max_per_page fleft">
    [?php
      $maxPerPages = array();
      $message = __('elements per page');
      foreach(sfConfig::get('dm_admin_max_per_page', array(10)) as $maxPerPage)
      {
        $maxPerPages[$maxPerPage] = $maxPerPage.' '.$message;
      }

      $currentMaxPerPage = $sf_user->getAttribute(
	      '<?php echo $this->getModuleName() ?>.max_per_page',
	      <?php echo isset($this->config['list']['max_per_page']) ? (integer) $this->config['list']['max_per_page'] : 10 ?>,
	      'admin_module'
	    );

      echo select_tag('max_per_page', options_for_select($maxPerPages, $currentMaxPerPage), array('id' => '__RAND_ME__')); ?]
  </div>

  [?php if ($pager->haveToPaginate()): ?]

  <a title="[?php echo __('First page'); ?]" href="[?php echo url_for('<?php echo $this->getUrlForAction('list') ?>') ?]?page=1">
    <span class="s16block s16_first">&nbsp;</span>
  </a>

  [?php foreach ($pager->getLinks() as $page): ?]
    [?php if ($page == $pager->getPage()): ?]
      <span>[?php echo $page ?]</span>
    [?php else: ?]
      <a href="[?php echo url_for('<?php echo $this->getUrlForAction('list') ?>') ?]?page=[?php echo $page ?]">[?php echo $page ?]</a>
    [?php endif; ?]
  [?php endforeach; ?]

  <a title="[?php echo __('Last page'); ?]" href="[?php echo url_for('<?php echo $this->getUrlForAction('list') ?>') ?]?page=[?php echo $pager->getLastPage() ?]">
    <span class="s16block s16_last">&nbsp;</span>
  </a>

  [?php endif; ?]

</div>
