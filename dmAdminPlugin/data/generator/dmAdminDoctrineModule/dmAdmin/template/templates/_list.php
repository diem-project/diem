<div class="sf_admin_list">
  [?php if (!$pager->getNbResults()): ?]
    <h2>[?php echo __('No result') ?]</h2>
  [?php else: ?]
    <table>
      <thead>
        <tr>
<?php if ($this->configuration->getValue('list.batch_actions')): ?>
          <th><input class="sf_admin_list_batch_checkbox" type="checkbox" /></th>
<?php endif; ?>
          [?php include_partial('<?php echo $this->getModuleName() ?>/list_th_<?php echo $this->configuration->getValue('list.layout') ?>', array('sort' => $sort)) ?]
        </tr>
      </thead>
      <tfoot>
        <tr>
<?php if ($this->configuration->getValue('list.batch_actions')): ?>
          <th><input class="sf_admin_list_batch_checkbox" type="checkbox" /></th>
<?php endif; ?>
          [?php include_partial('<?php echo $this->getModuleName() ?>/list_th_<?php echo $this->configuration->getValue('list.layout') ?>', array('sort' => $sort, 'security_manager' => $security_manager)) ?]
        </tr>
      </tfoot>
      <tbody class='{toggle_url: "[?php echo £link('@'.$helper->getUrlForAction('toggleBoolean'))->getHref() ?]"}'>
        [?php foreach ($pager->getResults() as $i => $<?php echo $this->getSingularName() ?>): $odd = fmod(++$i, 2) ? 'odd' : 'even' ?]
          <tr class="sf_admin_row [?php echo $odd ?] {pk: [?php echo $<?php echo $this->getSingularName() ?>->getPrimaryKey() ?]}">
<?php if ($this->configuration->getValue('list.batch_actions')): ?>
            <td>
              <input type="checkbox" name="ids[]" value="[?php echo $<?php echo $this->getSingularName() ?>->getPrimaryKey() ?]" class="sf_admin_batch_checkbox" />
            </td>
<?php endif; ?>
            [?php include_partial('<?php echo $this->getModuleName() ?>/list_td_<?php echo $this->configuration->getValue('list.layout') ?>', array('<?php echo $this->getSingularName() ?>' => $<?php echo $this->getSingularName() ?>, 'helper' => $helper, 'security_manager' => $security_manager)) ?]
          </tr>
        [?php endforeach; ?]
      </tbody>
    </table>
  [?php endif; ?]
</div>