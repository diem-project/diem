<?php

class dmWidgetContentTextForm extends dmWidgetContentImageForm
{

  protected $target = array(
      '_self' => 'Same window',
      '_blank' => 'New window'
  );
    
  public function configure()
  {
    parent::configure();
    
    $this->widgetSchema['title'] = new sfWidgetFormInputText();
    $this->validatorSchema['title'] = new sfValidatorString(array('required' => false));
    
    $this->widgetSchema['titleLink'] = new sfWidgetFormInputText(array(), array(
      'class' => 'dm_link_droppable',
      'title' => $this->__('Accepts pages, medias and urls')
    ));
    $this->validatorSchema['titleLink'] = new dmValidatorLinkUrl(array('required' => false));

    $this->widgetSchema['text'] = new sfWidgetFormTextarea();
    $this->validatorSchema['text'] = new sfValidatorString(array('required' => false));
    
    $this->widgetSchema['mediaLink'] = new sfWidgetFormInputText(array(), array(
      'class' => 'dm_link_droppable',
      'title' => $this->__('Accepts pages, medias and urls')
    ));
    $this->validatorSchema['mediaLink'] = new dmValidatorLinkUrl(array('required' => false));
    
    $this->widgetSchema['titlePosition'] = new sfWidgetFormChoice(array(
      'choices' => array('outside' => 'Outside', 'inside' => 'Inside')
    ));
    $this->validatorSchema['titlePosition'] = new sfValidatorChoice(array(
      'choices' => array('outside', 'inside')
    ));
    
    $this->widgetSchema['titlePosition']->setLabel('Title position');

    // delete the media association
    if($this->getDefault('mediaId'))
    {
      $this->widgetSchema['removeMedia'] = new sfWidgetFormInputCheckbox();
      $this->validatorSchema['removeMedia'] = new  sfValidatorBoolean(array('required' => false));
      $this->widgetSchema['removeMedia']->setLabel('Remove');
    }

    
    $this->widgetSchema['titleLinkTarget'] = new sfWidgetFormSelect(array(
        'choices' => $this->getI18n()->translateArray($this->target)        
    ));    
    $this->validatorSchema['titleLinkTarget'] = new sfValidatorChoice(array(
        'choices' => array_keys($this->target),
        'required' => false
    ));
    if (is_null($this->getDefault('titleLinkTarget'))) $this->setDefault ('titleLinkTarget', '_self');
    
    $this->widgetSchema['mediaLinkTarget'] = new sfWidgetFormSelect(array(
        'choices' => $this->getI18n()->translateArray($this->target)        
    ));    
    $this->validatorSchema['mediaLinkTarget'] = new sfValidatorChoice(array(
        'choices' => array_keys($this->target),
        'required' => false
    ));
    if (is_null($this->getDefault('mediaLinkTarget'))) $this->setDefault ('mediaLinkTarget', '_self');
    
    
    //unset the media link
    unset($this['link']);
  }

  public function getWidgetValues()
  {
    $values = parent::getWidgetValues();
    
    if(dmArray::get($values, 'removeMedia'))
    {
      $values['mediaId'] = null;
    }

    unset($values['removeMedia']);

    return $values;
  }

  public function getStylesheets()
  {
    return array(
      'lib.ui-tabs',
      'lib.markitup',
      'lib.markitupSet',
      'lib.ui-resizable'
    );
  }

  public function getJavascripts()
  {
    return array(
      'lib.ui-tabs',
      'lib.markitup',
      'lib.markitupSet',
      'lib.ui-resizable',
      'lib.fieldSelection',
      'core.tabForm',
      'core.markdown'
    );
  }
  
  protected function renderContent($attributes)
  {
    return $this->getHelper()->renderPartial('dmWidget', 'forms/dmWidgetContentText', array(
      'form' => $this,
      'baseTabId' => 'dm_widget_text_'.$this->dmWidget->get('id'),
      'hasMedia' => (boolean) $this->getValueOrDefault('mediaId')
    ));
  }
  
  /*
   * Disable media source validation
   * because a text widget may have no media
   */
  public function checkMediaSource($validator, $values)
  {
    return $values;
  }
}