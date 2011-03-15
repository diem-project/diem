<?php if($this->configuration->getValue('list.object_actions')):?>
<td>
  <ul class="sf_admin_td_actions">
<?php foreach ($this->configuration->getValue('list.object_actions') as $name => $params): ?>
<?php if ('_delete' == $name): ?>
    [?php if($security_manager->userHasCredentials('delete', $<?php echo $this->getSingularName()?>)): ?]
    <?php echo $this->addCredentialCondition('[?php echo $helper->linkToDelete($'.$this->getSingularName().', '.$this->asPhp($params).') ?]', $params) ?>
		[?php endif; ?]
<?php elseif ('_edit' == $name): ?>
    [?php if($security_manager->userHasCredentials('edit', $<?php echo $this->getSingularName()?>)): ?]
    <?php echo $this->addCredentialCondition('[?php echo $helper->linkToEdit($'.$this->getSingularName().', '.$this->asPhp($params).') ?]', $params) ?>
		[?php endif; ?]

<?php else: ?>
    [?php if($security_manager->userHasCredentials('<?php echo $name?>', $<?php echo $this->getSingularName()?>)): ?]
      <li class="sf_admin_action_<?php echo $params['class_suffix'] ?>">
		  [?php if (method_exists($helper, 'linkTo<?php echo $method = ucfirst(sfInflector::camelize($name)) ?>')): ?]
  <?php echo $this->addCredentialCondition('[?php echo $helper->linkTo'.$method.'($' . $this->getSingularName() . ', '.$this->asPhp($params).') ?]', $params) ?>
		  [?php else: ?]
  <?php echo $this->addCredentialCondition($this->getLinkToAction($name, $params, true), $params) ?>
  		[?php endif; ?]
      </li>
		[?php endif; ?]
<?php endif; ?>
<?php endforeach; ?>
  </ul>
</td>
<?php endif;?>