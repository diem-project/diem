<?php

class dmFrontBaseComponents extends dmBaseComponents
{
  protected
  $formManager;
  
  /**
   * Initializes this component.
   *
   * @param sfContext $context    The current application context.
   * @param string    $moduleName The module name.
   * @param string    $actionName The action name.
   *
   * @return boolean true, if initialization completes successfully, otherwise false
   */
  public function initialize($context, $moduleName, $actionName)
  {
    parent::initialize($context, $moduleName, $actionName);
    
    $this->formManager = $context->get('form_manager');
  }
  
  /*
   * @return DmPage the current page
   */
  public function getPage()
  {
    return $this->context->getPage();
  }
}