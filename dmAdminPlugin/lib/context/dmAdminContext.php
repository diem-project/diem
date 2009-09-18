<?php

class dmAdminContext extends dmContext
{
  protected
  $moduleType,
  $moduleSpace;

  /*
   * return boolean if current module and action match $module && $action
   */
  public function isModuleAction($module, $action)
  {
    return $this->sfContext->getModuleName() === $module && $this->sfContext->getActionName() === $action;
  }

  /*
   * @return dmModule a module
   */
  public function getModule()
  {
    return dmModuleManager::getModuleOrNull($this->sfContext->getModuleName());
  }

  public function getModuleType()
  {
    if (null === $this->moduleType)
    {
      $this->moduleType = dmModuleManager::getTypeBySlug($this->sfContext->getRequest()->getParameter('moduleTypeName'), false);
    }
    return $this->moduleType;
  }

  public function getModuleSpace()
  {
    if (null === $this->moduleSpace)
    {
      if($moduleType = $this->getModuleType())
      {
        $this->moduleSpace = $moduleType->getSpaceBySlug($this->sfContext->getRequest()->getParameter('moduleSpaceName'), false);
      }
      else
      {
        $this->moduleSpace = false;
      }
    }
    return $this->moduleSpace;
  }


  public static function createInstance(sfContext $sfContext)
  {
    return self::$instance = new self($sfContext);
  }

}