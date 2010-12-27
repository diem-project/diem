<?php
/*
 *
 */

/**
 *
 * Enter description here ...
 * @author serard
 *
 */
class dmModuleActionRecordSecurityStrategy extends dmModuleSecurityStrategyAbstract
{

  /**
   * With this strategy, there's nothing to do on module generation
   * 
   * (non-PHPdoc)
   * @see dmModuleSecurityStrategyAbstract::secure()
   */
  public function secure(dmModule $module, $app, $actionName, $actionConfig)
  {
    //does nothing
  }
}