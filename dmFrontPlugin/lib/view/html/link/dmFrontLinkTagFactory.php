<?php

class dmFrontLinkTagFactory extends dmBaseLinkTagFactory
{
  
  public function buildLink($source)
  {
    try
    {
      $this->serviceContainer->setParameter(
        'link_tag.resource',
        $resource = $this->serviceContainer->getService('link_resource')->initialize($source)
      );
      
      return $this->serviceContainer->getService('link_tag_'.$resource->getType());
    }
    catch(Exception $e)
    {
      /*
       * if the dm_debug mode is enabled, or
       * if an exception occured when building an error link
       * stop here.
       */
      if (sfConfig::get('dm_debug') || $source instanceof Exception)
      {
        throw $e;
      }

      // return an error link
      return $this->buildLink($e);
    }
  }
}