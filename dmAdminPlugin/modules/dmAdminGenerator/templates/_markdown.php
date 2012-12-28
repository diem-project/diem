<?php
  use_stylesheet('lib.markitup');
  use_stylesheet('lib.markitupSet');
  use_stylesheet('lib.ui-resizable');
  use_javascript('lib.ui-resizable');
  use_javascript('lib.markitup');
  use_javascript('lib.markitupSet');
  use_javascript('core.markdown');
  
  $uniqueCode = dmString::random();

  $attributes = array_merge(
    $attributes instanceof sfOutputEscaper ? $attributes->getRawValue() : $attributes,
    array('class' => 'dm_markdown '.json_encode(array('code' => $uniqueCode)))
  );
?>

<div class="<?php echo $class ?><?php $form[$name]->hasError() and print ' errors' ?>">
  <?php if ($form[$name]->hasError()): ?>
    <div class="error">
      <div class="s16 s16_error"><?php echo $form[$name]->renderError() ?></div>
    </div>
  <?php endif; ?>
  <div class="sf_admin_form_row_inner clearfix">
    <?php
      echo $form[$name]->renderLabel($label, array('class' => 'fnone'));
      echo $form[$name]->render($attributes);
      if ($help)
      {
        echo '<div class="help">'.__($help).'</div>';
      }
      elseif($help = $form[$name]->renderHelp())
      {
        echo '<div class="help">'.$help.'</div>';
      }
    ?>
  </div>
</div>

<div class="markdown_preview_wrap sf_admin_form_row">
  <div class="sf_admin_form_row_inner">
    <label class="fnone"><?php echo __('Preview'); ?></label>
    <div class="markdown_preview markdown" id="dm_markdown_preview_<?php echo $uniqueCode ?>">
      <?php echo markdown($form->getObject()->get($name)) ?>
    </div>
  </div>
</div>