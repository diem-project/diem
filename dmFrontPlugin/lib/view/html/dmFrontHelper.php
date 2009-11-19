<?php

class dmFrontHelper extends dmHelper
{
  
  public function £link($source = null)
  {
    try
    {
      $linkTagObject = $this->getLinkTag($source);
    }
    catch(Exception $e)
    {
      if (sfConfig::get('dm_debug') || $source instanceof Exception)
      {
        throw $e;
      }
      else
      {
        $linkTagObject = $this->getLinkTag($e);
      }
    }

    return $linkTagObject;
  }
  
  protected function getLinkTag($source)
  {
    $resource = $this->serviceContainer->getService('link_resource');
    $resource->initialize($source);
    
    $this->serviceContainer->setParameter('link_tag.class', $this->serviceContainer->getParameter('link_tag_'.$resource->getType().'.class'));
    $this->serviceContainer->setParameter('link_tag.source', $resource);
    
    return $this->serviceContainer->getService('link_tag');
  }
  
}