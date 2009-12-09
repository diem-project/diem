<?php

class dmAdminLinkTagFactory extends dmBaseLinkTagFactory
{
  
  public function buildLink($source)
  {
    $this->serviceContainer->setParameter('link_tag.source', $source);
    
    return $this->serviceContainer->getService('link_tag');
  }
}