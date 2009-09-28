<?php

if ($listActions = $this->configuration->getValue('list.batch_actions'))
{
  echo '<div class="sf_admin_actions clearfix">';
  
  foreach ((array) $listActions as $action => $params)
  {
    echo $this->addCredentialCondition('<input class="dm_js_confirm" title="[?php echo __(\''.$params['label'].'\') ?]" type="submit" name="'.$action.'" value="[?php echo __(\''.$params['label'].'\') ?]" disabled="disabled"/>', $params);
  }
  
  echo '</div>';
}