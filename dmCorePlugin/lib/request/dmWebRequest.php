<?php

class dmWebRequest extends sfWebRequest
{

  protected
  $absoluteUrlRoot;

  public function getAbsoluteUrlRoot()
  {
    if(null === $this->absoluteUrlRoot)
    {
      $this->absoluteUrlRoot = $this->getUriPrefix().$this->getRelativeUrlRoot();
    }
    
    return $this->absoluteUrlRoot;
  }

  /**
   * Returns true if the request is a XMLHttpRequest.
   * @return bool true if the request is an XMLHttpRequest, false otherwise
   */
  public function isXmlHttpRequest()
  {
    /*
     * When a file is submitted during an ajax request,
     * parent::isXmlHttpRequest() returns false,
     * so we have to specify the request parameter dm_xhr
     * to tell the server the request is asynchronous
     */
    return parent::isXmlHttpRequest() || $this->getParameter('dm_xhr');
  }

  public function isFlashRequest()
  {
    return $this->getParameter('dm_flash');
  }
  
  /**
   * Returns the request context used.
   *
   * @return array An array of values representing the current request
   */
  public function getRequestContext()
  {
    $context = parent::getRequestContext();
    
    $context['relative_url_root'] = $this->getRelativeUrlRoot();
    $context['absolute_url_root'] = $this->getAbsoluteUrlRoot();
    $context['script_name']       = $this->getScriptName();
    
    return $context;
  }

}