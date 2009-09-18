<?php

class dmFrontLinkTagMedia extends dmFrontLinkTag
{
  protected
  $media;

  protected function configure()
  {
    $this->media = $this->get('source');
  }
  
  protected function getBaseHref()
  {
    return dm::getRequest()->getAbsoluteUrlRoot().'/'.$this->media->getWebPath();
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