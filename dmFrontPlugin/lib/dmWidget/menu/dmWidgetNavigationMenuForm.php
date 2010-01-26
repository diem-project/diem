<?php

class dmWidgetNavigationMenuForm extends dmWidgetPluginForm
{

  public function configure()
  {
    if (!$this->getDefault('items'))
    {
      $this->setDefault('items', array());
    }
    
    parent::configure();
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

}