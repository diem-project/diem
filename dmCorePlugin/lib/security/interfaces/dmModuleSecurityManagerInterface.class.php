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
  public function secure(dmModule $module);

  /**
   * Should/must be called by secure() at its end, to make
   * all instanciated strategies securely end their processes.
   * For example, there could be opened files to save and close.
   * Strategies will have their ->secure() method called by this one.
   */
  public function save();
}