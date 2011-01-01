<?php

if ($listActions = $this->configuration->getValue('list.batch_actions'))
{
  echo '<div class="sf_admin_actions">';
  
  foreach ((array) $listActions as $action => $params)
  {
    echo $this->addCredentialCondition('<input class="dm_js_confirm" title="[?php echo __(\''.$params['label'].'\', array(), \'' . $this->getModule()->getOption('i18n_catalogue') . '\') ?]" type="submit" name="'.$action.'" value="[?php echo __(\''.$params['label'].'\') ?]" disabled="disabled"/>', $params);
  }
  
  echo '</div>';
}