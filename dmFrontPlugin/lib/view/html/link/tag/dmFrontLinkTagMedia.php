<?php

class dmFrontLinkTagMedia extends dmFrontLinkTag
{
  protected
  $media;

  protected function initialize()
  {
    parent::initialize();
    
    $this->media = $this->resource->getSubject();
  }
  
  protected function getBaseHref()
  {
    return $this->requestContext['absolute_url_root'].'/'.$this->media->getWebPath();
  }

  protected function renderText()
  {
    if (isset($this->options['text']))
    {
      return $this->options['text'];
    }

    return $this->media->get('file');
  }

}