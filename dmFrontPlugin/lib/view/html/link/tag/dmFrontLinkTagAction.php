<?php

class dmFrontLinkTagAction extends dmFrontLinkTag
{
  protected
  $action;
  
  protected function initialize()
  {
    parent::initialize();
    
    $this->action = $this->resource->getSubject();
  }

  protected function getBaseHref()
  {
    return self::$dmContext->getService('controller')->genUrl($this->action);
  }

}