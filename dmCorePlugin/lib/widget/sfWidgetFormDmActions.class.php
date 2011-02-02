<?php
class sfWidgetFormDmActions extends sfWidgetFormChoice
{
  public function __construct($options = array(), $attributes = array())
  {
    $choices = array();
    $modules = array();
    foreach(dmContext::getInstance()->getModuleManager()->getModules() as $module)
    {
      $actions = $module->getActions();
      foreach($actions as $action)
      {
        $choiceName = $module->getName() . '|' . $action['name'];
        $choices[$module->getName() . '|' . $action['name']] = $module->getName() . ' | ' . $action['name'];
      }
    }
    $options['choices'] = $choices;
    parent::__construct($options, $attributes);
  }
}