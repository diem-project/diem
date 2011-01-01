<?php foreach ($this->configuration->getValue('list.display') as $name => $field): ?>

[?php ob_start(); ?]

<th class="sf_admin_<?php echo strtolower($field->getType()) ?> sf_admin_list_th_<?php echo $name ?>">
<?php if ($field->isReal()): ?>
  [?php $translatedLabel = __('<?php echo $field->getConfig('label', '', true) ?>', array(), '<?php echo $this->getModule()->getOption('i18n_catalogue')?>'); ?]
  [?php if ('<?php echo $name ?>' == $sort[0]): ?]
    [?php echo link_to($translatedLabel, '@<?php echo $this->getUrlForAction('list') ?>', array('class' => 's16 s16_sort_'.$sort[1], 'query_string' => 'sort=<?php echo $name ?>&sort_type='.($sort[1] == 'asc' ? 'desc' : 'asc'), 'title' => __('Sort by %field%', array('%field%' => $translatedLabel), 'dm'))) ?]
  [?php else: ?]
    [?php echo link_to($translatedLabel, '@<?php echo $this->getUrlForAction('list') ?>', array('class' => 's16 s16_right_little', 'query_string' => 'sort=<?php echo $name ?>&sort_type=asc', 'title' => __('Sort by %field%', array('%field%' => $translatedLabel), 'dm'))) ?]
  [?php endif; ?]
<?php else: ?>
  [?php echo __('<?php echo $field->getConfig('label', '', true) ?>', array(), '<?php echo $this->getModule()->getOption('i18n_catalogue')?>') ?]
<?php endif; ?>
</th>

[?php $currentHeader = ob_get_clean(); ?]

<?php echo $this->addCredentialCondition("[?php print \$currentHeader ?]", $field->getConfig()) ?>
<?php endforeach; ?>

<?php if($this->configuration->getValue('list.object_actions', false)):?>
<th class="sf_admin sf_admin_list_th_object_action<?php echo $name ?>">
[?php echo __('Actions', array(), 'dm');?]
</th>
<?php endif;?>