<?php
/*
 *
 *
 *
 */


/**
 *
 * @author serard
 *
 */
abstract class dmModuleSecurityStrategyAbstract extends dmModuleSecurityAbstract implements dmModuleSecurityStrategyInterface
{
	
	protected $module;
	protected $action;

  /**
   * This method is called by dmModuleSecurityManager->secure() method for modules
   * having a diem's security descriptor in their modules.yml description.
   *
   * @param dmModule $module the module to secure
   * @param string $actionName the action's name to secure
   * @param array $actionConfig the module-action's configuration
   */
  public function secure(dmModule $module, $actionName, $actionConfig)
  {
  	
  }

  /**
   * Sets the module for the following calls.
   * This method is called by dmModuleSecurity->getStrategy on first strategy creation.
   * 
   * @param dmModule $module
   * @return dmModuleSecurityStrategyAbstract
   */
  public function setModule(dmModule $module)
  {
    $this->module = $module;
    return $this;
  }

  public function setAction($action)
  {
  	$this->action = $action;
  	return $this;
  }
  
  /**
   * This method is called by dmModuleSecurityManager->secure() method
   * at the end of its processing.
   * 
   */
  public function save()
  {
    $this->clearCache();
  }
}