<?php

class DmPageFrontNewForm extends DmPageForm
{

  public function configure()
  {
    parent::configure();

    $this->useFields(array());
    
    $this->widgetSchema['name'] = new sfWidgetFormInputText();
    $this->validatorSchema['name'] = new sfValidatorString(array('max_length' => 255));
    
    $this->widgetSchema['slug'] = new sfWidgetFormInputText();
    $this->validatorSchema['slug'] = new sfValidatorString(array('max_length' => 255));

    $this->widgetSchema['dm_layout_id'] = new sfWidgetFormDoctrineChoice(array(
      'model' => 'DmLayout',
      'add_empty' => false
    ));
    $this->validatorSchema['dm_layout_id'] = new sfValidatorDoctrineChoice(array(
      'model' => 'DmLayout'
    ));
    
    $parentChoices = $this->getParentChoices();
    
    $this->widgetSchema['parent_id'] = new sfWidgetFormChoice(array(
      'choices' => $parentChoices
    ));
    $this->validatorSchema['parent_id'] = new sfValidatorChoice(array(
      'choices' => array_keys($parentChoices)
    ));
    
    $this->widgetSchema['dm_layout_id']->setLabel('Layout');

    $this->mergePostValidator(new sfValidatorCallback(array('callback' => array($this, 'checkSlug'))));
  }
  
  protected function getParentChoices()
  {
    $_parentChoices = dmDb::query('DmPage p')
    ->where('p.record_id = 0')
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
    $parent = dmDb::table('DmPage')->find($values['parent_id']);
    
    if (!$parent instanceof DmPage)
    {
      throw new dmException('Create page with unknown parent '.$values['parent_id']);
    }
    
    parent::doUpdateObject($values);
    
    $page = $this->getObject();
    
    $page->module = $parent->module;
    
    $action = dmString::modulize(str_replace('-', '_', dmString::slugify($values['name'])));
    
    if (dmDb::query('DmPage p')->where('p.module = ? AND p.action = ?', array($page->module, $action))->exists())
    {
      $iterator = 2;
      while(dmDb::query('DmPage p')->where('p.module = ? AND p.action = ?', array($page->module, $action.$iterator))->exists())
      {
        $iterator++;
      }
      $action .= $iterator;
    }
    
    $page->action = $action;
    
    $page->title = $page->name;

    $page->Node->insertAsLastChildOf($parent);
    
    $pageView = $page->PageView;
        
    $pageView->dmLayoutId = $values['dm_layout_id'];
  }
  
  public function checkSlug($validator, $values)
  {
    if (!empty($values['slug']))
    {
      $values['slug'] = dmString::slugify($values['slug'], true);
      
      $existingPageName = dmDb::query('DmPageTranslation t')
      ->where('t.lang = ? AND t.slug = ?', array(dm::getUser()->getCulture(), $values['slug']))
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

}