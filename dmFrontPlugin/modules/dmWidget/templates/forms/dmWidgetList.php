<?php

echo $form->renderGlobalErrors();

echo _open('ul.dm_form_elements');

echo
_tag('li.dm_form_element.multi_inputs.sort.clearfix',
  $form['orderField']->renderLabel(__('Order by')).
  $form['orderField']->render().
  $form['orderType']->render(array('class' => 'ml10'))
).
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

/*
 * Show list filters
 */
foreach($form as $widgetName => $widget)
{
  if('Filter' === substr($widgetName, -6))
  {
    echo $widget->renderRow();
  }
}

echo $form['cssClass']->renderRow();

echo _close('ul');