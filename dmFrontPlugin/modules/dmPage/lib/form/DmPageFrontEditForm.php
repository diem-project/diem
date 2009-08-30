<?php

class DmPageFrontEditForm extends DmPageForm
{
	protected
	$page;

  public function configure()
  {
  	parent::configure();
  	
  	$this->page = $this->getObject();
  	
  	$this->mergeI18nForm();

    $this->useFields(array('id', 'module', 'action', 'slug', 'name', 'title', 'h1', 'description', 'keywords', 'is_active', 'is_secure'), false);
    
    if(!sfConfig::get('dm_seo_use_keywords'))
    {
    	unset($this['keywords']);
    }
    else
    {
      $this->widgetSchema['keywords'] = new sfWidgetFormTextarea(array(), array('rows' => 2));
      $this->setDefault('keywords', $this->page->keywords);
    }
    
    $this->widgetSchema['description'] = new sfWidgetFormTextarea(array(), array('rows' => 2));
    
    $this->validatorSchema['slug'] = new sfValidatorString(array(
      'required' => !$this->page->Node->isRoot()
    ));
    
    $this->widgetSchema['dm_layout_id'] = new sfWidgetFormDoctrineChoice(array(
      'model' => 'DmLayout',
      'add_empty' => false
    ));
    $this->validatorSchema['dm_layout_id'] = new sfValidatorDoctrineChoice(array(
      'model' => 'DmLayout'
    ));
    
    if (!$this->page->Node->isRoot() && !$this->page->isAutomatic)
    {
	    $parentChoices = $this->getParentChoices();
	    
	    $this->widgetSchema['parent_id'] = new sfWidgetFormChoice(array(
	      'choices' => $parentChoices
	    ));
	    $this->validatorSchema['parent_id'] = new sfValidatorChoice(array(
	      'choices' => array_keys($parentChoices),
	      'required' => !$this->page->Node->isRoot()
	    ));
	    
	    $this->setDefault('parent_id', $this->page->getNodeParentId());
    }
    
    $this->widgetSchema['dm_layout_id']->setLabel('Layout');
    $this->widgetSchema['description']->setLabel('Desc');
    $this->widgetSchema['is_active']->setLabel('Available');
    $this->widgetSchema['is_secure']->setLabel('Requires authentification');
    
    if ($this->page->Node->isRoot())
    {
    	foreach(array('slug', 'module', 'action') as $fieldName)
    	{
    		$this->widgetSchema[$fieldName]->setAttribute('readonly', true);
    	}
    }

    $this->mergePostValidator(new sfValidatorCallback(array('callback' => array($this, 'checkSlug'))));

    $this->mergePostValidator(new sfValidatorCallback(array('callback' => array($this, 'checkModuleAction'))));
    
    $this->setDefaults(array(
      'dm_layout_id' => $this->page->PageView->dmLayoutId,
      'name'      => $this->page->name,
      'slug'      => $this->page->slug,
      'title'     => $this->page->title,
      'h1'        => $this->page->h1,
      'description' => $this->page->description,
      'is_active' => $this->page->is_active
    ));
  }
  
  public function mergeI18nForm()
  {
    $class = $this->getI18nFormClass();

    $i18nObject = $this->object->Translation[dm::getUser()->getCulture()];
    
    $i18n = new $class($i18nObject);
    unset($i18n['id'], $i18n['lang']);

    $this->mergeForm($i18n);
  }
  
  public function embedCurrentI18n($decorator = null)
  {
  	return;
  }
  
  protected function getParentChoices()
  {
    $_parentChoices = dmDb::query('DmPage p')
    ->where('p.record_id = 0 AND ( lft < ? OR rgt > ? )', array($this->page->lft, $this->page->rgt))
    ->orderBy('p.lft')
    ->withI18n()
    ->select('p.id, p.level, translation.name')
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
	    
	    if ($values['parent_id'] != $this->page->nodeParentId)
	    {
	      $this->page->Node->moveAsLastChildOf(dmDb::table('DmPage')->find($values['parent_id']));
	    }
    }
    
    $this->page->PageView->dmLayoutId = $values['dm_layout_id'];
    
    parent::doUpdateObject($values);
  }
  
  public function checkSlug($validator, $values)
  {
    if (!empty($values['slug']))
    {
      $values['slug'] = dmString::slugify($values['slug'], true);
      
      $existingPageName = dmDb::query('DmPageTranslation t')
      ->where('t.lang = ? AND t.slug = ? AND t.id != ?', array(dm::getUser()->getCulture(), $values['slug'], $this->page->id))
      ->select('t.name')
      ->fetchValue();
      
      if($existingPageName)
      {
        $error = new sfValidatorError($validator, dm::getI18n()->__('The page "%1%" uses this slug', array('%1%' => $existingPageName)));
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
      $values['module'] = dmString::modulize(str_replace('-', '_', dmString::slugify($values['module'])));
      $values['action'] = dmString::modulize(str_replace('-', '_', dmString::slugify($values['action'])));
      
      $existingPage = dmDb::query('DmPage p')
      ->where('p.module = ? AND p.action = ? and p.record_id = ? AND p.id != ?', array($values['module'], $values['action'], $this->page->record_id, $this->page->id))
      ->fetchRecord();
      
      if($existingPage)
      {
        $error = new sfValidatorError($validator, dm::getI18n()->__('The page "%1%" uses this module.action', array('%1%' => $existingPage->name)));
        // throw an error bound to the password field
        throw new sfValidatorErrorSchema($validator, array('action' => $error));
      }
    }

    return $values;
  }

}