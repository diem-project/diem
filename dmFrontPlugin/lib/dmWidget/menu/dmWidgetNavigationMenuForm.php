<?php

class dmWidgetNavigationMenuForm extends dmWidgetPluginForm
{

  public function configure()
  {
    $this->validatorSchema['link'] = new sfValidatorPass();
    $this->validatorSchema['text'] = new sfValidatorPass();
    $this->validatorSchema['secure'] = new sfValidatorPass();
    $this->validatorSchema['nofollow'] = new sfValidatorPass();
    $this->validatorSchema['depth'] = new sfValidatorPass();
    $this->validatorSchema['target'] = new sfValidatorPass();

    if (!$this->getDefault('items'))
    {
      $this->setDefault('items', array());
    }

    parent::configure();

    $this->widgetSchema['ulClass']      = new sfWidgetFormInputText(array(
      'label' => 'UL CSS class'
    ));
    $this->validatorSchema['ulClass']   = new dmValidatorCssClasses(array('required' => false));

    $this->widgetSchema['menuName']      = new sfWidgetFormInputText(array(
      'label' => 'Menu name'
    ));
    $this->validatorSchema['menuName']   = new sfValidatorString(array('required' => false));
    $this->widgetSchema->setHelp('menuName', 'Used for id generation');
    
    $this->widgetSchema['liClass']      = new sfWidgetFormInputText(array(
      'label' => 'LI CSS class'
    ));
    $this->validatorSchema['liClass']   = new dmValidatorCssClasses(array('required' => false));

    if($this->getService('user')->can('system'))
    {
      $this->widgetSchema['menuClass']      = new sfWidgetFormInputText(array(
        'label' => 'Menu PHP class'
      ));
      $this->validatorSchema['menuClass']   = new dmValidatorPhpClass(array(
        'required' => false,
        'implements' => 'dmMenu'
      ));

      $this->widgetSchema->setHelp('menuClass', sprintf('PHP Class used to render the menu (default: %s)',
        $this->getServiceContainer()->getParameter('menu.class')
      ));
    }
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

    foreach(dmArray::get($values, 'link', array()) as $index => $link)
    {
      $values['items'][] = array(
        'link'     => $values['link'][$index],
        'text'     => $values['text'][$index],
        'secure'   => (int) !empty($values['secure'][$index]),
        'nofollow' => (int) !empty($values['nofollow'][$index]),
        'depth'    => $values['depth'][$index],
        'target'    => $values['target'][$index]
      );
    }

    unset(
      $values['link'],
      $values['text'],
      $values['secure'],
      $values['nofollow'],
      $values['depth'],
      $values['target']
    );

    return $values;
  }
}
