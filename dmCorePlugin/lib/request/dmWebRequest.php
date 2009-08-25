<?php

class dmWebRequest extends sfWebRequest
{

	protected
	$absoluteUrlRoot;

	public function getParameters()
	{
		return $this->getParameterHolder()->getAll();
	}

  public function getAbsoluteUrlRoot()
  {
  	if(is_null($this->absoluteUrlRoot))
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

}