<?php

class dmWidgetContentTextForm extends dmWidgetContentImageForm
{

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
    $this->widgetSchema['removeMedia'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['removeMedia'] = new  sfValidatorBoolean(array('required' => false));
    $this->widgetSchema['removeMedia']->setLabel('Remove');

    //unset the media link
    unset($this['link']);
  }

  public function getWidgetValues()
  {
    $values = parent::getWidgetValues();
    
    if($values['removeMedia'])
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