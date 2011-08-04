<?php

abstract class dmFrontLinkTag extends dmBaseLinkTag
{
  protected
  $requestContext;
  
  public function __construct(dmFrontLinkResource $resource, array $requestContext, array $options = array())
  {
    $this->resource       = $resource;
    $this->requestContext = $requestContext;
    
    $this->initialize($options);
  }

  protected function initialize(array $options = array())
  {
    parent::initialize($options);
    
    if ($this->resource->hasParams())
    {
      $this->options['params'] = $this->resource->getParams();
    }
  }
  
  protected function renderText()
  {
    if (isset($this->options['text']))
    {
      return $this->options['text'];
    }

    return $this->getBaseHref();
  }
  
  public function getHrefPrefix()
  {
    return sfConfig::get('sf_no_script_name')
    ? $this->requestContext['prefix']
    : $this->requestContext['script_name'];
  }

  public function getAbsoluteHref()
  {
    $href = $this->getHref();

    if(strpos($href, '://'))
    {
      return $href;
    }

    $prefix = $this->requestContext['uri_prefix'];
    
    if($this->isHttpSecure())
    {
    	$prefix = str_replace('http://', 'https://', $prefix);
    }
    return $prefix.$href;
  }
}