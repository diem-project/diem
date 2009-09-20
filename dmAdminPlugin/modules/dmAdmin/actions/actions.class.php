<?php

class dmAdminActions extends dmAdminBaseActions
{

  public function executeModuleSpace(sfWebRequest $request)
  {
    $this->forward404Unless(
      $this->type = dmModuleManager::getTypeBySlug($request->getParameter('moduleTypeName'), false),
      sprintf('%s is not a module type', $request->getParameter('moduleTypeName'))
    );

    $this->forward404Unless(
      $this->space = $this->type->getSpaceBySlug($request->getParameter('moduleSpaceName'), false),
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
      $this->type = $this->context->getModuleType(),
      sprintf('%s is not a module type', $request->getParameter('moduleTypeName'))
    );

    $this->spaces = $this->type->getSpaces();
  }

  public function executeIndex(sfWebRequest $request)
  {
    require_once(dmOs::join(sfConfig::get('dm_admin_dir'), 'modules/dmUserLog/lib/dmUserLogViewLittle.php'));
    require_once(dmOs::join(sfConfig::get('dm_admin_dir'), 'modules/dmActionLog/lib/dmActionLogViewLittle.php'));
    
    $this->userLogView = new dmUserLogViewLittle($this->context->get('user_log'), $this->context->getI18n(), $this->getUser()->getCulture());
    
    $this->actionLogView = new dmActionLogViewLittle($this->context->get('action_log'), $this->context->getI18n(), $this->getUser()->getCulture());
  }
  
  public function executeRefreshLogs(dmWebRequest $request)
  {
    $parts = array();
    
    $userLog = $this->context->get('user_log');
    $userHash = $userLog->getStateHash();
    
    if ($userHash == $request->getParameter('user_hash'))
    {
      $parts[0] = '-';
    }
    else
    {
      require_once(dmOs::join(sfConfig::get('dm_admin_dir'), 'modules/dmUserLog/lib/dmUserLogViewLittle.php'));
      $view = new dmUserLogViewLittle($userLog, $this->context->getI18n(), $this->getUser()->getCulture());
      $parts[0] = $view->renderBody(10);
    }
    $parts[1] = $userHash;
    
    $actionLog = $this->context->get('action_log');
    $actionHash = $actionLog->getStateHash();
    
    if ($actionHash == $request->getParameter('action_hash'))
    {
      $parts[2] = '-';
    }
    else
    {
      require_once(dmOs::join(sfConfig::get('dm_admin_dir'), 'modules/dmActionLog/lib/dmActionLogViewLittle.php'));
      $view = new dmActionLogViewLittle($actionLog, $this->context->getI18n(), $this->getUser()->getCulture());
      $parts[2] = $view->renderBody(10);
    }
    $parts[3] = $actionHash;
    
    return $this->renderText(implode('__DM_SPLIT__', $parts));
  }
  
  public function executeNothing()
  {
    
  }

}