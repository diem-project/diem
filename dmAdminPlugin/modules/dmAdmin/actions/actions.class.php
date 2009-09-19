<?php

class dmAdminActions extends dmAdminBaseActions
{

  public function executeModuleSpace(sfWebRequest $request)
  {
    $this->forward404Unless(
      $this->type = $this->dmContext->getModuleType(),
      sprintf('%s is not a module type', $request->getParameter('moduleTypeName'))
    );

    $this->forward404Unless(
      $this->space = $this->dmContext->getModuleSpace(),
      sprintf('%s is not a module space in %s type', $request->getParameter('moduleTypeName'), $request->getParameter('moduleTypeName'))
    );

    $this->modules = $this->space->getModules();
    
    foreach($this->modules as $index => $module)
    {
      if (!$this->context->getRouting()->hasRouteName($module->getUnderscore()))
      {
        unset($this->modules[$index]);
      }
    }
  }

  public function executeModuleType(sfWebRequest $request)
  {
    $this->forward404Unless(
      $this->type = $this->dmContext->getModuleType(),
      sprintf('%s is not a module type', $request->getParameter('moduleTypeName'))
    );

    $this->spaces = $this->type->getSpaces();
  }

  public function executeIndex(sfWebRequest $request)
  {
    require_once(dmOs::join(sfConfig::get('dm_admin_dir'), 'modules/dmUserLog/lib/dmUserLogViewLittle.php'));
    
    $this->userLogView = new dmUserLogViewLittle($this->dmContext->getService('user_log'), $this->getUser()->getCulture());
    
    $this->userLogOptions = array(
      'delay' => 1000,
      'refresh_url' => dmAdminLinkTag::build('dmUserLog/refresh?view=little&max=10')->getHref()
    );
//    $this->diemSize = dm::getDiemSize();

  }
  
  public function executeNothing()
  {
    
  }

}