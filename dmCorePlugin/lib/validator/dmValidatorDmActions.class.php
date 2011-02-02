<?php
class dmValidatorDmActions extends sfValidatorChoice
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
        $choices[$choiceName] = $choiceName;
      }
    }
    $options['choices'] = array_combine($choices, $choices);
    parent::__construct($options, $attributes);
  }
}