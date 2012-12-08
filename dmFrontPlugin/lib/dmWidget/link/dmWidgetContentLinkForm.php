<?php

class dmWidgetContentLinkForm extends dmWidgetPluginForm
{

  protected $target = array(
      '_self' => 'Same window',
      '_blank' => 'New window'
  );
    
  public function configure()
  {
    $this->widgetSchema['href']     = new sfWidgetFormInputText(array(), array(
      'class' => 'dm_link_droppable',
      'title' => $this->__('Accepts pages, medias and urls')
    ));
    
    $this->validatorSchema['href']  = new dmValidatorLinkUrl(array('required' => true));
    
    $this->widgetSchema['target'] = new sfWidgetFormSelect(array(
        'choices' => $this->getI18n()->translateArray($this->target)        
    ));    
    $this->validatorSchema['target'] = new sfValidatorChoice(array(
        'choices' => array_keys($this->target),
        'required' => false
    ));
    if (is_null($this->getDefault('target'))) $this->setDefault ('target', '_self');
    
    $this->widgetSchema['text']     = new sfWidgetFormTextarea(array(), array('rows' => 2));
    $this->validatorSchema['text']  = new sfValidatorString(array('required' => false));

    $this->widgetSchema['title']    = new sfWidgetFormInputText();
    $this->validatorSchema['title'] = new sfValidatorString(array('required' => false));

    parent::configure();
  }

}