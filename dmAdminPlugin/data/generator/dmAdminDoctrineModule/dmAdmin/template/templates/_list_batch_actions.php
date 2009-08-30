<?php if ($listActions = $this->configuration->getValue('list.batch_actions')): ?>
<li class="sf_admin_batch_actions_choice">
  <select name="batch_action">
<?php foreach ((array) $listActions as $action => $params): ?>
    <?php echo $this->addCredentialCondition('<option value="'.$action.'">[?php echo __(\''.$params['label'].'\') ?]</option>', $params) ?>
<?php endforeach; ?>
  </select>
  [?php $form = new BaseForm(); if ($form->isCSRFProtected()): ?]
    <input type="hidden" name="[?php echo $form->getCSRFFieldName() ?]" value="[?php echo $form->getCSRFToken() ?]" />
  [?php endif; ?]
  <input type="submit" value="[?php echo __('Act on selection') ?]" />
</li>
<?php endif; ?>
