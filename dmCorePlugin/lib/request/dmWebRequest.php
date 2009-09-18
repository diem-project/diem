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
   *
   * It works if your JavaScript library set an X-Requested-With HTTP header.
   * Works with Prototype, Mootools, jQuery, and perhaps others.
   *
   * @return bool true if the request is an XMLHttpRequest, false otherwise
   */
  public function isXmlHttpRequest()
  {
    return parent::isXmlHttpRequest() || $this->getParameter('dm_xhr');
  }

  public function isFlashRequest()
  {
    return $this->getParameter('dm_flash');
  }

  public function useTidy()
  {
    return dmHtml::isEnabled() && !$this->getParameter('dm_tidy_disable');
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