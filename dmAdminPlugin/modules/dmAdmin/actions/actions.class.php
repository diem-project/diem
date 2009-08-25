<?php

class dmAdminActions extends dmAdminBaseActions
{

  public function executeModuleSpace(sfWebRequest $request)
  {
    $this->forward404Unless(
      $this->type = dmAdminContext::getInstance()->getModuleType(),
      sprintf('%s is not a module type', $request->getParameter('moduleTypeName'))
    );

    $this->forward404Unless(
      $this->space = dmAdminContext::getInstance()->getModuleSpace(),
      sprintf('%s is not a module space in %s type', $request->getParameter('moduleTypeName'), $request->getParameter('moduleTypeName'))
    );

    $this->modules = $this->space->getModules();
  }

  public function executeModuleType(sfWebRequest $request)
  {
    $this->forward404Unless(
      $this->type = dmAdminContext::getInstance()->getModuleType(),
      sprintf('%s is not a module type', $request->getParameter('moduleTypeName'))
    );

    $this->spaces = $this->type->getSpaces();
  }

  public function executeIndex(sfWebRequest $request)
  {
//    $this->diemSize = dm::getDiemSize();

  }

}