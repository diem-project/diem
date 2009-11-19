<?php

class dmFrontBaseActions extends dmBaseActions
{
  protected
  $forms;
  
  /**
   * Initializes this component.
   *
   * @param sfContext $context    The current application context.
   * @param string    $moduleName The module name.
   * @param string    $actionName The action name.
   *
   * @return boolean true, if initialization completes successfully, otherwise false
   */
  public function initialize($context, $moduleName, $actionName)
  {
    parent::initialize($context, $moduleName, $actionName);
    
    $this->forms = $context->get('form_manager');
  }
  
  /*
   * @return DmPage the current page
   */
  public function getPage()
  {
    return $this->context->getPage();
  }

  /**
   * Indicates that this action requires security.
   *
   * @return bool true, if this action requires security, otherwise false.
   */
  public function isSecure()
  {
    if (!dmConfig::get('site_active'))
    {
      return true;
    }

    return parent::isSecure();
  }

  /**
   * Gets credentials the user must have to access this action.
   *
   * @return mixed An array or a string describing the credentials the user must have to access this action
   */
  public function getCredential()
  {
    $credentials = parent::getCredential();

    if (!dmConfig::get('site_active'))
    {
      $credentials = (array) $credentials;
      $credentials[] = 'site_view';
    }

    return $credentials;
  }
  
  protected function redirectBack()
  {
    $refererUrl = $this->request->getReferer();

    if (!$refererUrl || $refererUrl === $this->request->getUri())
    {
      if ($page = $this->getPage())
      {
        $refererUrl = $this->context->getHelper()->£link($page)->getAbsoluteHref();
      }
      else
      {
        $refererUrl = $this->context->getHelper()->£link()->getAbsoluteHref();
      }
    }
    
    return $this->redirect($refererUrl);
  }
  
  /*
   * Preload all pages related to records
   */
  protected function preloadPages($records)
  {
    dmDb::table('DmPage')->preloadPagesForRecords($records);
  }
}