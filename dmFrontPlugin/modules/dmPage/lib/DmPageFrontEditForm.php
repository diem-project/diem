<?php

class DmPageFrontEditForm extends DmPageForm
{
  protected
  $page;

  public function configure()
  {
    parent::configure();
    
    $this->useFields(array('id', 'module', 'action', 'slug', 'name', 'title', 'h1', 'description', 'keywords', 'is_active', 'is_secure', 'credentials', 'is_indexable'), false);
    
    if(!sfConfig::get('dm_seo_use_keywords'))
    {
      unset($this['keywords']);
    }
    else
    {
      $this->widgetSchema['keywords'] = new sfWidgetFormTextarea(array(), array('rows' => 2));
      $this->setDefault('keywords', $this->object->keywords);
    }
    
    $this->widgetSchema['description'] = new sfWidgetFormTextarea(array(), array('rows' => 2));
    
    $this->validatorSchema['slug'] = new sfValidatorString(array(
      'required' => !$this->object->getNode()->isRoot()
    ));
    
    $this->widgetSchema['dm_layout_id'] = new sfWidgetFormDoctrineChoice(array(
      'model' => 'DmLayout',
      'add_empty' => false
    ));
    $this->validatorSchema['dm_layout_id'] = new sfValidatorDoctrineChoice(array(
      'model' => 'DmLayout'
    ));
    
    if (!$this->object->getNode()->isRoot() && !$this->object->isAutomatic)
    {
      $parentChoices = $this->getParentChoices();
      
      $this->widgetSchema['parent_id'] = new sfWidgetFormChoice(array(
        'choices' => $parentChoices
      ));
      $this->validatorSchema['parent_id'] = new sfValidatorChoice(array(
        'choices' => array_keys($parentChoices),
        'required' => !$this->object->getNode()->isRoot()
      ));
    }
    
    $this->widgetSchema['dm_layout_id']->setLabel('Layout');
    $this->widgetSchema['description']->setLabel('Desc');
    $this->widgetSchema['is_active']->setLabel('Available');
    $this->widgetSchema['is_secure']->setLabel('Requires authentication');
    $this->widgetSchema['is_indexable']->setLabel('Search engine crawlers');
    
    if ($this->object->getNode()->isRoot())
    {
      foreach(array('slug', 'module', 'action') as $fieldName)
      {
        $this->widgetSchema[$fieldName]->setAttribute('readonly', true);
      }
    }

    $this->mergePostValidator(new sfValidatorCallback(array('callback' => array($this, 'checkSlug'))));

    $this->mergePostValidator(new sfValidatorCallback(array('callback' => array($this, 'checkModuleAction'))));
    
    $this->setDefaults(array(
      'dm_layout_id' => $this->object->PageView->dmLayoutId,
      'name'      => $this->object->name,
      'slug'      => $this->object->slug,
      'title'     => $this->object->title,
      'h1'        => $this->object->h1,
      'description' => $this->object->description,
      'keywords'  => $this->object->keywords,
      'is_active' => $this->object->is_active,
      'is_secure' => $this->object->is_secure,
      'credentials' => $this->object->credentials,
      'is_indexable' => $this->object->is_indexable,
      'parent_id' => $this->object->getNodeParentId()
    ));
  }
  
  protected function getParentChoices()
  {
    $_parentChoices = dmDb::query('DmPage p')
    ->where('p.record_id = 0 AND ( lft < ? OR rgt > ? )', array($this->object->lft, $this->object->rgt))
    ->orderBy('p.lft')
    ->withI18n()
    ->select('p.id, p.level, pTranslation.name')
    ->fetchPDO();
    
    $parentChoices = array();
    foreach($_parentChoices as $values)
    {
      $parentChoices[$values[0]] = str_repeat('&nbsp;&nbsp;', $values[1]).'-&nbsp;'.$values[2];
    }
    
    return $parentChoices;
  }

  protected function doUpdateObject($values)
  {
    if (isset($values['parent_id']))
    {
      if (!dmDb::query('DmPage p')->where('p.id = ?', $values['parent_id'])->exists())
      {
        throw new dmException('Move page to unknown parent '.$values['parent_id']);
      }
      
      if ($values['parent_id'] != $this->object->getNodeParentId())
      {
        $this->object->getNode()->moveAsLastChildOf(dmDb::table('DmPage')->find($values['parent_id']));
      }
    }
    
    $this->object->PageView->dmLayoutId = $values['dm_layout_id'];
    
    parent::doUpdateObject($values);
  }
  
  public function checkSlug($validator, $values)
  {
    if (!empty($values['slug']))
    {
      $values['slug'] = dmString::urlize($values['slug'], true);
      
      $existingPageName = dmDb::query('DmPageTranslation t')
      ->where('t.lang = ? AND t.slug = ? AND t.id != ?', array($this->object->lang, $values['slug'], $this->object->id))
      ->select('t.name')
      ->fetchValue();
      
      if($existingPageName)
      {
        $error = new sfValidatorError($validator, $this->getI18n()->__('The page "%1%" uses this slug', array('%1%' => $existingPageName)));
        // throw an error bound to the password field
        throw new sfValidatorErrorSchema($validator, array('slug' => $error));
      }
    }

    return $values;
  }
  
  public function checkModuleAction($validator, $values)
  {
    if (!empty($values['module']) && !empty($values['action']))
    {
      
      foreach(array('module', 'action') as $key)
      {
        $values[$key] = dmString::modulize(str_replace('-', '_', dmString::slugify(dmString::underscore($values[$key]))));
      }

      $existingPage = dmDb::query('DmPage p')
      ->where('p.module = ? AND p.action = ? and p.record_id = ? AND p.id != ?', array($values['module'], $values['action'], $this->object->record_id, $this->object->id))
      ->fetchRecord();
      
      if($existingPage)
      {
        $error = new sfValidatorError($validator, $this->getI18n()->__('The page "%1%" uses this module.action', array('%1%' => $existingPage->name)));
        // throw an error bound to the password field
        throw new sfValidatorErrorSchema($validator, array('action' => $error));
      }
    }

    return $values;
  }

}