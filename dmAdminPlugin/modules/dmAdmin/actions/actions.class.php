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
  	$this->site = $this->getDmContext()->getSite();

    require_once(dmOs::join(sfConfig::get('dm_admin_dir'), 'modules/dmUserLog/lib/dmUserLogViewLittle.php'));
  	
  	$this->userLogView = new dmUserLogViewLittle(new dmUserLog, $this->getUser()->getCulture());
  	
    $this->userLogOptions = array(
      'delay' => 1000,
      'refresh_url' => dmAdminLinkTag::build('dmUserLog/refresh?view=little&max=10')->getHref()
    );
//    $this->diemSize = dm::getDiemSize();

  }

}