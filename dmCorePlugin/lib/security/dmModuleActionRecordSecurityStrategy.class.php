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
class dmModuleActionRecordSecurityStrategy extends dmMicroCache
{
  public function secure(dmModule $module, $actionName, $actionConfig)
  {

  }

  public function manageAuto(dmModule $module, $actionName, $actionConfig)
  {
    $do = "nothgin";
  }
}