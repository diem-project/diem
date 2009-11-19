<?php

abstract class dmFrontLinkTag extends dmLinkTag
{
  
  public function __construct(dmFrontLinkResource $resource, array $requestContext)
  {
    $this->resource       = $resource;
    $this->requestContext = $requestContext;
    
    $this->initialize();
  }

  protected function initialize()
  {
    parent::initialize();
    
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