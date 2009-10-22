<?php

class BasedmAdminActions extends dmAdminBaseActions
{
  public function executeIndex(dmWebRequest $request)
  {
  }
  
  
  public function executeNothing()
  {
    
  }
  
  public function executeModuleSpace(sfWebRequest $request)
  {
    $this->forward404Unless($this->type = $this->getModuleTypeBySlug($request->getParameter('moduleTypeName')), sprintf('%s is not a module type', $request->getParameter('moduleTypeName')));
  
    $slug = $request->getParameter('moduleSpaceName');
    foreach($this->type->getSpaces() as $space)
    {
      if (dmString::slugify($space->getPublicName()) == $slug)
      {
        $this->space = $space;
        break;
      }
    }

    $this->forward404Unless(
      isset($this->space), sprintf('%s is not a module space in %s type', $request->getParameter('moduleTypeName'), $request->getParameter('moduleTypeName'))
    );
    
    $this->menu = $this->context->get('admin_menu');
    
    $this->moduleManager = $this->context->get('module_manager');
  }

  public function executeModuleType(sfWebRequest $request)
  {
    $this->forward404Unless($this->type = $this->getModuleTypeBySlug($request->getParameter('moduleTypeName')), sprintf('%s is not a module type', $request->getParameter('moduleTypeName')));

    $this->spaces = $this->type->getSpaces();
    
    $this->menu = $this->context->get('admin_menu');
    
    $this->moduleManager = $this->context->get('module_manager');
  }
  
  protected function getModuleTypeBySlug($slug)
  {
    foreach($this->context->getModuleManager()->getTypes() as $type)
    {
      if (dmString::slugify($type->getPublicName()) == $slug)
      {
        return $type;
      }
    }
    
    return null;
  }
}