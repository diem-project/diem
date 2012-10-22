[?php // BATCH ACTIONS ?]
  
[?php include_partial('<?php echo $this->getModuleName() ?>/list_batch_actions', array('helper' => $helper)) ?]

[?php // LIENS  ?]

[?php include_partial('<?php echo $this->getModuleName() ?>/list_actions', array('helper' => $helper, 'configuration' => $configuration, 'pager' => $pager)) ?]

[?php //PAGINATION ?]

[?php include_partial('<?php echo $this->getModuleName() ?>/list_pagination', array('helper' => $helper, 'pager' => $pager)) ?]

[?php // MAX PER PAGE

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
$maxPerPageSelect = new sfWidgetFormSelect(array('choices' => $maxPerPages));
echo $maxPerPageSelect->render('dm_max_per_page', $currentMaxPerPage, array(
  'id' => '__DM_RANDOM_ID__',
  'class' => 'dm_max_per_page',
  'disabled' => $pager->getNbResults() === 0
));
?]