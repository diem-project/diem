<?php $saveParams = dmArray::get($this->configuration->getValue('edit.actions'), '_save'); ?>
<ul>
[?php
echo $helper->linkToSave($form->getObject(), <?php echo $this->asPhp($saveParams) ?>);
?]
<li class="ml10"><input type="submit" value="[?php echo __('Close') ?]" onclick="parent.document.getElementById('cboxClose').setAttribute('rel', 'dm_close');return false;" /></li>
</ul>