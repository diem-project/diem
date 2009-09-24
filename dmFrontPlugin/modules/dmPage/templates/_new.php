<?php

echo £o('div.dm.dm_page_add');

echo £('div.form',

  $form->open('.dm_form.list.little').

  £('ul.dm_form_elements',
    $form['parent_id']->renderRow().
    $form['name']->renderRow().
    $form['slug']->renderRow().
    $form['dm_layout_id']->renderRow()
  ).
  sprintf(
      '<div class="actions">
        <div class="actions_part clearfix">
          %s
          %s
        </div>
      </div>',
      sprintf('<a class="cancel dm close_dialog dm button fleft">%s</a>', dm::getI18n()->__('Cancel')),
      sprintf('<input type="submit" class="submit and_save green fright" name="and_save" value="%s" />', dm::getI18n()->__('Save'))
    ).
  
  $form->close()
);

echo £('div.parent_slugs.none', $parentSlugsJson);

echo £c('div');