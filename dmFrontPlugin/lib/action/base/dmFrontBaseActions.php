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
    
    $this->forms = $this->getService('form_manager');
  }
  
  /**
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
        $refererUrl = $this->getHelper()->link($page)->getAbsoluteHref();
      }
      else
      {
        $refererUrl = $this->getHelper()->link()->getAbsoluteHref();
      }
    }
    
    return $this->redirect($refererUrl);
  }
  
  /**
   * Preload all pages related to records
   */
  protected function preloadPages($records)
  {
    dmDb::table('DmPage')->preloadPagesForRecords($records);
  }
  
  /**
   * Return model record id of current DmPage
   * 
   * @return integer|null id of model record for current DmPage
   */
  protected function getPageRecordId()
  {
    if (!$this->getPage())
    {
      return null;
    }

    return $this->getPage()->getRecordId();
  }
  
  /**
   * Return model record name of current DmPage
   *
   * @return string name of model record for current DmPage
   */
  protected function getPageModel()
  {
    if (!$this->getPage())
    {
      return null;
    }
    if (!is_object($this->getPage()->getRecord()))
    {
      return null;
    }
    
    return get_class($this->getPage()->getRecord());
  }

  /**
   * Return record instance of current DmPage
   *
   * @return object record instance of current DmPage
   */ 
  protected function getPageRecord()
  {
    if (!$this->getPage())
    {
      return null;
    }

    return $this->getPage()->getRecord();
  }
}