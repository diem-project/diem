<?php

class dmWidgetNavigationMenuForm extends dmWidgetPluginForm
{

  public function configure()
  {
    $this->validatorSchema['link'] = new sfValidatorPass();
    $this->validatorSchema['text'] = new sfValidatorPass();

    if (!$this->getDefault('items'))
    {
      $this->setDefault('items', array());
    }
    
    parent::configure();

    $this->widgetSchema['ulClass']      = new sfWidgetFormInputText();
    $this->validatorSchema['ulClass']   = new dmValidatorCssClasses(array('required' => false));

    $this->widgetSchema['ulClass']->setLabel('UL CSS class');

    $this->widgetSchema['liClass']      = new sfWidgetFormInputText();
    $this->validatorSchema['liClass']   = new dmValidatorCssClasses(array('required' => false));

    $this->widgetSchema['liClass']->setLabel('LI CSS class');
  }

  public function getStylesheets()
  {
    return array(
      'lib.ui-tabs',
      'front.widgetMenuForm'
    );
  }

  public function getJavascripts()
  {
    return array(
      'lib.ui-tabs',
      'core.tabForm',
      'front.widgetMenuForm'
    );
  }

  protected function renderContent($attributes)
  {
    return $this->getHelper()->renderPartial('dmWidget', 'forms/dmWidgetNavigationMenu', array(
      'form' => $this,
      'items' => $this->getValueOrDefault('items'),
      'baseTabId' => 'dm_widget_menu_'.$this->dmWidget->get('id')
    ));
  }

  public function getWidgetValues()
  {
    $values = parent::getWidgetValues();

    $values['items'] = array();

    foreach($values['link'] as $index => $link)
    {
      $values['items'][] = array(
        'link'  => $values['link'][$index],
        'text'  => $values['text'][$index]
      );
    }

    unset($values['link'], $values['text']);

    return $values;
  }
}