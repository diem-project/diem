<?php 
/*
 * 
 * 
 */

/**
 * 
 * @author serard
 *
 */
interface dmModuleSecurityManagerInterface
{
  /**
   * Secures a dmModule according to its security options 
   * @param dmModule $module
   */
  public function secure(dmModule $module = null);

  /**
  * Sets the module for the instance
  * @param dmModule $module
  */
  public function setModule(dmModule $module);

   /**
   * Returns the corresponding strategy for
   * the action kind and the given required strategy.
   *
   * The strategies and action kinds must be declared accordingly in
   * services.yml
   *
   * @param string $strategy
   * @param string $actionKind actions|components|whatever declared in services.yml
   */
  public function getStrategy($strategy, $actionKind, $module = null, $action = null);
}
