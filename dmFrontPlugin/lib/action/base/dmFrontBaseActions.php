<?php

class dmFrontBaseActions extends dmBaseActions
{

  /**
   * Forward the request to the page that matches the given source
   **/
  public function renderPage($source)
  {
    $page = dmDb::table('DmPage')->findOneBySource($source);
    return $this->forwardToSlug($page->slug);
  }

  /**
   * Forward the request to the page that matches the given slug
   **/
  public function forwardToSlug($slug)
  {
    $this->getRequest()->setParameter('slug', $slug);
    return $this->forward('dmFront', 'page');
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
}
