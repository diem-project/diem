<?php

class dmFrontLinkTagAction extends dmFrontLinkTag
{
  protected
  $action,
  $controller;
  
  public function __construct(dmFrontLinkResource $resource, sfWebController $controller, array $requestContext, array $options = array())
  {
    $this->controller     = $controller;
    
    parent::__construct($resource, $requestContext, $options);
  }
  
  protected function initialize(array $options = array())
  {
    parent::initialize($options);
    
    $this->action = $this->resource->getSubject();
  }

  protected function getBaseHref()
  {
    return $this->controller->genUrl($this->action);
  }

}