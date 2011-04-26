[?php if ($configuration->getSortable()): ?]
<div class="sf_admin_action sf_admin_action_sort">
  <?php if ($this->getModule()->getTable()->isSortable()) echo $this->getLinkToAction('sort', array('action' => 'sortTable', 'params' => array('class' => 'dm_sort s16 s16_sort')), false); ?>
  <?php if ($this->getModule()->getTable()->isNestedSet()) echo $this->getLinkToAction('sortTree', array('action' => 'sortTree', 'params' => array('class' => 'dm_sort s16 s16_sort')), false); ?>
</div>
[?php endif; ?]

<?php if ($actions = $this->configuration->getValue('list.actions')): ?>
<?php foreach ($actions as $name => $params): ?>
  <div class="sf_admin_action sf_admin_action_<?php echo $params['class_suffix'] ?>">
<?php if ('_new' == $name): ?>
<?php echo $this->addCredentialCondition('[?php echo $helper->linkToNew('.$this->asPhp($params).') ?]', $params) ?>
<?php else: ?>
    <?php echo $this->addCredentialCondition($this->getLinkToAction($name, $params, false), $params) ?>
<?php endif; ?>
  </div>
<?php endforeach; ?>
<?php endif; ?>