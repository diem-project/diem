<?php

class dmFrontLinkTagUri extends dmFrontLinkTag
{
  protected
  $uri,
  $controller;
  
  public function __construct(dmFrontLinkResource $resource, sfWebController $controller, array $requestContext, array $options = array())
  {
    $this->controller = $controller;
    
    parent::__construct($resource, $requestContext, $options);
  }
  
  protected function initialize(array $options = array())
  {
    parent::initialize($options);
    
    $this->uri = $this->resource->getSubject();
  }

  protected function getBaseHref()
  {
    if (strncmp($this->uri, '#', 1) === 0 || strncmp($this->uri, 'mailto:', 7)  === 0)
    {
      return $this->uri;
    }
    
    return $this->controller->genUrl($this->uri);
  }

}