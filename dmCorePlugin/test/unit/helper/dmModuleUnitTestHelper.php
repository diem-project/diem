<?php

require_once(dirname(__FILE__).'/dmUnitTestHelper.php');

class dmModuleUnitTestHelper extends dmUnitTestHelper
{
  public function getPathKeys($moduleKey, $includeModule = false)
  {
    if (is_array($path = $this->moduleManager->getModule($moduleKey)->getPath($includeModule)))
    {
      return array_keys($path);
    }
    return null;
  }

  public function hasAncestor($moduleKey1, $moduleKey2)
  {
    return $this->moduleManager->getModule($moduleKey1)->hasAncestor($moduleKey2);
  }
  
  public function hasDescendant($moduleKey1, $moduleKey2)
  {
    return $this->moduleManager->getModule($moduleKey1)->hasDescendant($moduleKey2);
  }

  public function hasNearestAncestorWithPage($moduleKey1, $moduleKey2)
  {
    return $this->moduleManager->getModule($moduleKey1)->getNearestAncestorWithPage() === $this->getModule($moduleKey2);
  }

  public function getFarthestAncestor($moduleKey)
  {
    return $this->moduleManager->getModule($moduleKey)->getFarthestAncestor();
  }

//  public function getDir($moduleKey)
//  {
//    return $this->moduleManager->getModule($moduleKey)->getDir();
//  }
}