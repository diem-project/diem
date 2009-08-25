[?php $dm_list_pagination = get_partial('<?php echo $this->getModuleName() ?>/dm_list_pagination', array('pager' => $pager, 'helper' => $helper, 'class' => 'dm_pagination_top')); ?]
<div class="sf_admin_list">
  <div class="dm_pagination dm_pagination_top">
    [?php echo str_replace('__RAND_ME__', dmString::random(8), $dm_list_pagination); ?]
  </div>
  [?php if (!$pager->getNbResults()): ?]
    <h2>[?php echo __('No result') ?]</h2>
  [?php else: ?]
    <table>
      <thead>
        <tr>
<?php if ($this->configuration->getValue('list.batch_actions')): ?>
          <th id="sf_admin_list_batch_actions"><input id="sf_admin_list_batch_checkbox" type="checkbox" /></th>
<?php endif; ?>
          [?php include_partial('<?php echo $this->getModuleName() ?>/list_th_<?php echo $this->configuration->getValue('list.layout') ?>', array('sort' => $sort)) ?]
        </tr>
      </thead>
      <tfoot>
        <tr>
          <th colspan="<?php echo count($this->configuration->getValue('list.display'))+1 ?>">
            <div class="dm_pagination_status">
              [?php echo format_number_choice('[0] no result|[1] 1 result|(1,+Inf] %1% results', array('%1%' => $pager->getNbResults()), $pager->getNbResults()) ?]
              [?php if ($pager->haveToPaginate()): ?]
                [?php echo __('(page %%page%%/%%nb_pages%%)', array('%%page%%' => $pager->getPage(), '%%nb_pages%%' => $pager->getLastPage())) ?]
              [?php endif; ?]
            </div>
            <ul class="sf_admin_actions clearfix">
				      [?php include_partial('<?php echo $this->getModuleName() ?>/list_batch_actions', array('helper' => $helper)) ?]
				    </ul>
          </th>
        </tr>
      </tfoot>
      <tbody>
        [?php foreach ($pager->getResults() as $i => $<?php echo $this->getSingularName() ?>): $odd = fmod(++$i, 2) ? 'odd' : 'even' ?]
          <tr class="sf_admin_row [?php echo $odd ?]">
<?php if ($this->configuration->getValue('list.batch_actions')): ?>
            [?php include_partial('<?php echo $this->getModuleName() ?>/list_td_batch_actions', array('<?php echo $this->getSingularName() ?>' => $<?php echo $this->getSingularName() ?>, 'helper' => $helper)) ?]
<?php endif; ?>
            [?php include_partial('<?php echo $this->getModuleName() ?>/list_td_<?php echo $this->configuration->getValue('list.layout') ?>', array('<?php echo $this->getSingularName() ?>' => $<?php echo $this->getSingularName() ?>)) ?]
          </tr>
        [?php endforeach; ?]
      </tbody>
    </table>
	  <div class="dm_pagination dm_pagination_bottom">
      [?php echo str_replace('__RAND_ME__', dmString::random(8), $dm_list_pagination); ?]
	  </div>
  [?php endif; ?]
</div>

[?php if($sf_user->can('loremize')): ?]
<div class="dm_export">
<?php echo $this->getLinkToAction('Export CSV', array('action' => 'export', 'params' => array('class' => 'dm_sort s16 s16_export')), false); ?>
</div>
[?php endif; ?]

[?php if($sf_user->can('loremize')): ?]
<div class="dm_loremize">
<p class="dm_sort s16 s16_edit fleft">Loremize :</p>
<?php foreach(array(1, 5, 10, 20, 50) as $nbRecords): ?>
  [?php
    echo £link('dmService/launch?name=dmLoremize&module_name=<?php echo $this->getModuleName() ?>&nb=<?php echo $nbRecords ?>')
    ->name('<?php echo $nbRecords ?>')
    ->set('.ml10');
  ?]
<?php endforeach; ?>
</div>
[?php endif; ?]