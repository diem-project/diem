<?php

class BasedmAdminActions extends dmAdminBaseActions
{
  public function executeIndex(sfWebRequest $request)
  {
    require_once(dmOs::join(sfConfig::get('dm_admin_dir'), 'modules/dmUserLog/lib/dmUserLogViewLittle.php'));
    require_once(dmOs::join(sfConfig::get('dm_admin_dir'), 'modules/dmActionLog/lib/dmActionLogViewLittle.php'));
    
    $this->userLogView = new dmUserLogViewLittle($this->context->get('user_log'), $this->context->getI18n(), $this->getUser());
    
    $this->actionLogView = new dmActionLogViewLittle($this->context->get('action_log'), $this->context->getI18n(), $this->getUser());
  }
  
  public function executeRefreshLogs(dmWebRequest $request)
  {
    $data = array();
    $nbRecords = 6;
    
    foreach(array('user', 'action') as $logName)
    {
      $log = $this->context->get($logName.'_log');
      $data[$logName] = array('hash' => $log->getStateHash());
      
      if ($data[$logName]['hash'] == $request->getParameter($logName.'_hash'))
      {
        unset($data[$logName]);
      }
      else
      {
        $viewClass = sprintf('dm%sLogViewLittle', dmString::camelize($logName));
        require_once(sprintf(
          dmOs::join(sfConfig::get('dm_admin_dir'), 'modules/dm%sLog/lib/%s.php'),
          dmString::camelize($logName), $viewClass
        ));
        $view = new $viewClass($log, $this->context->getI18n(), $this->getUser());
        $data[$logName]['html'] = $view->renderBody($nbRecords);
      }
    }
    
    return $this->renderJson($data);
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