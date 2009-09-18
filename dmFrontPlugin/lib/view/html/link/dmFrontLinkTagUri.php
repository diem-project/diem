<?php

class dmFrontLinkTagUri extends dmFrontLinkTag
{
  protected
  $uri;

  protected function configure()
  {
    $this->uri = $this->get('source');
  }

  protected function getBaseHref()
  {
    if (strncmp($this->uri, '#', 1) === 0)
    {
      return $this->uri;
    }
    
    return sfContext::getInstance()->getController()->genUrl($this->uri);
  }

}