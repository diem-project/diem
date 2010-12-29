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
interface dmModuleSecurityStrategyInterface
{
  /**
   * Secures a given $app/$module/$action using $actionConfig
   * 
   * @param dmModule $module the module to secure
   * @param unknown_type $app the application of the module
   * @param unknown_type $actionName the action of module to secure
   * @param unknown_type $actionConfig the security configuration of the action
   */
  public function secure(dmModule $module, $actionName, $actionConfig);
  
  /**
   * When secure() process is over (managed by dmModuleSecurityManagerInterface), 
   * all instanciated strategies have to save()
   */
  public function save();
  
  /**
   * Returns boolean is user has needed credentials to act on module/action
   */
  public function userHasCredentials();
}