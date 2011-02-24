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

  public function getDefaultOptions()
  {
    return array_merge(parent::getDefaultOptions(), array(
      'external_blank' => false
    ));
  }
  
  protected function initialize(array $options = array())
  {
    parent::initialize($options);

    $this->addAttributeToRemove(array('external_blank'));
    
    $this->uri = $this->resource->getSubject();

    if($this->options['external_blank'] && 0 !== strncmp($this->uri, $this->requestContext['absolute_url_root'], strlen($this->requestContext['absolute_url_root'])) && 0 !== strncmp($this->uri, $this->requestContext['prefix'], strlen($this->requestContext['prefix'])) )
    {
      $this->target('_blank');
    }
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