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
    return parent::isXmlHttpRequest();
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