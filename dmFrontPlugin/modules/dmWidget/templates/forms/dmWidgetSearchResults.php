<?php

echo $form->renderGlobalErrors();

echo _open('ul.dm_form_elements');

echo
_tag('li.dm_form_element.multi_inputs.pagination.clearfix',
  $form['maxPerPage']->renderError().
  $form['maxPerPage']->renderLabel(__('Per page')).
  $form['maxPerPage']->render(array('class' => 'fleft')).
  _tag('div.checkbox_list',
    $form['navTop']->render(array('class' => 'ml10 fnone')).
    $form['navTop']->renderLabel('Top', array('class' => 'ml10 fnone')).
    $form['navBottom']->render(array('class' => 'ml10 fnone')).
    $form['navBottom']->renderLabel('Bottom', array('class' => 'ml10 fnone'))
  )
);

echo $form['cssClass']->renderRow();

echo _close('ul');