[?php

  $required = ($validator = $form->getValidatorSchema()->offsetGet($name)) ? $validator->getOption('required') : false;

  // embedded media forms are required if their file field is required
  $required && $form[$name] instanceof sfFormFieldSchema && ($formValidator = $form->getValidatorSchema()->offsetGet($name)) && $formValidator instanceof sfValidatorSchema && ($fileValidator = $formValidator->offsetGet('file')) && $required = $fileValidator->getOption('required');

  $divClass = dmArray::toHtmlCssClasses(array(
    $class,
    ($field->getConfig('is_big') || $field->getConfig('markdown')) ? 'big' : '',
    $field->getConfig('is_link') ? 'dm_link_droppable' : '',
    $required ? 'required' : ''
  ));

  if('empty_' === $name)
  {
    echo '<div class="'.$divClass.'"></div>';
    return;
  }

  if(empty($label))
  {
    $label = dmString::humanize($name);
  }

  $label = __($label, array(), '<?php echo $this->getModule()->getOption('i18n_catalogue')?>');

  if($form->getObject()->getTable()->isI18nColumn($name))
  {
    $label = _media('dmCore/images/flag-16/'.$sf_user->getCulture().'.png')
    ->size(16, 11)
    ->set('.dm_label_culture')
    ->alt(format_language($sf_user->getCulture())).
    $label;
  }

  if($required)
  {
    $label = $label.
    _media('dmCore/images/16/required.png')
    ->size(16, 16)
    ->set('.dm_label_required')
    ->alt(__('Required.', array(), 'sf_admin'));
  }
?]
[?php if ($field->isPartial()): ?]
  <div class="[?php echo $divClass ?]">[?php include_partial('<?php echo $this->getModuleName() ?>/'.$name, array('<?php echo $this->getModule()->getKey() ?>' => $form->getObject(), 'form' => $form, 'attributes' => $attributes instanceof sfOutputEscaper ? $attributes->getRawValue() : $attributes)) ?]</div>
[?php elseif ($field->isComponent()): ?]
  <div class="[?php echo $divClass ?]">[?php include_component('<?php echo $this->getModuleName() ?>', $name, array('<?php echo $this->getModuleName() ?>' => $form->getObject(), 'form' => $form, 'attributes' => $attributes instanceof sfOutputEscaper ? $attributes->getRawValue() : $attributes)) ?]</div>
[?php elseif ($field->getConfig('markdown')): ?]
  [?php include_partial("dmAdminGenerator/markdown", array("form" => $form, "field" => $field, "class" => $class, "name" => $name, "label" => $label, "attributes" => $attributes, "help" => $help)); ?]
[?php elseif(isset($form[$name])): ?]
  <div data-field-name="[?php echo $name ?]" class="[?php echo $divClass ?][?php $form[$name]->hasError() and print ' errors' ?]">
    [?php if ($form[$name]->hasError()): ?]
      <div class="error">
        <div class="s16 s16_error">[?php echo __((string) $form[$name]->getError(), array(), '<?php echo $this->getModule()->getOption('i18n_catalogue')?>') ?]</div>
      </div>
    [?php endif; ?]
    <div class="sf_admin_form_row_inner clearfix">
      [?php
      
      echo '<div class="label_wrap">';
      
      echo $form[$name]->renderLabel($label);
      
      if($form[$name]->getWidget() instanceof sfWidgetFormDoctrineChoice && $form[$name]->getWidget()->getOption('multiple'))
      {
        echo sprintf('<div class="control selection"><span class="select_all">%s</span><span class="unselect_all">%s</span><span class="see_selected">%s</span><span class="see_unselected">%s</span><span class="see_all">%s</span></div>', __('Select all', array(), 'dm'), __('Unselect all', array(), 'dm'), __('See selected', array(), 'dm'), __('See unselected', array(), 'dm'), __('See all', array(), 'dm'));
      }
      if($form[$name]->getWidget() instanceof sfWidgetFormDmDoctrineChoice)
      {
        $pager = $form[$name]->getWidget()->getPager();
        $pager->setMaxPerPage($sf_user->getAttribute('<?php echo $this->getModuleName()?>.' . $name . '.max_per_page', 10, 'admin_module'));
        $pager->init();
        $linkArray = $helper->getRouteArrayForAction('paginateRelation', $form->getObject());
        $linkArray['field'] = $name;
        ob_start();
        include_partial('<?php echo $this->getModuleName()?>/form_field_pagination', array('pager' => $pager, 'field' => $name, 'link' => url_for($linkArray)));
        $pagination = ob_get_clean();
        
        $checkbox_tools = sprintf('<div class="dm_checkbox_tools"><div class="dm_checkbox_search_filter"><input class="search-box" type="text" title="Search" value="%s"/><span class="clear"><a title="Clear search">X</a></span></div>%s</div>', isset($search) ? $search : '', $pagination);
        $resizer = '<div class="resize-handler"></div>';
      }else{
        $pagination = '';
      	$checkbox_tools = '';
      	$resizer = '';
      }
      
      echo '</div>';

      echo '<div class="content">'.$checkbox_tools.$form[$name]->render($attributes instanceof sfOutputEscaper ? $attributes->getRawValue() : $attributes). $resizer . '</div>';

      if ($help)
      {
        echo '<div class="help">'.__($help, array(), '<?php echo $this->getModule()->getOption('i18n_catalogue')?>').'</div>';
      }
      elseif($help = $form[$name]->renderHelp())
      {
        echo '<div class="help">'.$help.'</div>';
      }
      ?]
    </div>
  </div>
[?php else: //check if is a media view ?]
  <div class="[?php echo $divClass ?]">
    [?php
    $found = false;
    
    if ('dm_gallery' === $name)
    {
      $found = true;
      include_partial('dmMedia/galleryMedium', array('record' => $form->getObject()));
    }
    elseif (substr($name, -5) === '_view')
    {
      $found = true;
      include_partial('dmMedia/viewBig', array('object' => $form->getObject()->getDmMediaByColumnName(substr($name, 0, strlen($name)-5))));
    }
    elseif (substr($name, -5) === '_list')
    {
      if (!$relation = $form->getObject()->getTable()->getRelationHolder()->get($alias = dmString::camelize(substr($name, 0, strlen($name)-5))))
      {
        $relation = $form->getObject()->getTable()->getRelationHolder()->get($alias = substr($name, 0, strlen($name)-5));
      }
      if ($relation)
      {
        echo '<div class="sf_admin_form_row_inner clearfix">';
        echo '<div class="label_wrap">'.__($field->getConfig('label', '', true), array(), '<?php echo $this->getModule()->getOption('i18n_catalogue')?>').'</div>';
        echo $sf_context->getServiceContainer()->mergeParameter('related_records_view.options', array(
          'record' => $form->getObject(),
          'alias'  => $alias
        ))->getService('related_records_view')->render();
        echo '</div>';
        $found = true;
      }
    }

    if(!$found)
    {
      if (sfConfig::get('sf_debug'))
      {
        throw new dmException($name.' is not a valid form field');
      }
      else
      {
        echo '?';
      }
    }
    ?]
  </div>
[?php endif; ?]