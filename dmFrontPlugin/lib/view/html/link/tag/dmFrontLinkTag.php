<?php

abstract class dmFrontLinkTag extends dmBaseLinkTag
{
  
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
}