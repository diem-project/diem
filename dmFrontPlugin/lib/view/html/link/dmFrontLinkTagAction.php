<?php

class dmFrontLinkTagAction extends dmFrontLinkTag
{
  protected
  $action;
  
  protected function configure()
  {
    $this->action = $this->get('source');
  }

  protected function getBaseHref()
  {
    return sfContext::getInstance()->getController()->genUrl($this->action);
  }

}