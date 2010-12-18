<?php

/*
 * Generic class to handle media based widgets
 */
abstract class dmWidgetContentBaseMediaView extends dmWidgetPluginView
{
  
  public function configure()
  {
    parent::configure();

    $this->addRequiredVar('mediaId');
  }

  protected function filterViewVars(array $vars = array())
  {
    $vars = parent::filterViewVars($vars);
    
    if (!empty($vars['mediaId']) || $this->isRequiredVar('mediaId'))
    {
      $media = dmDb::table('DmMedia')->findOneByIdWithFolder($vars['mediaId']);
      
      if (!$media instanceof DmMedia)
      {
        throw new dmException('No DmMedia found for media id : '.$vars['mediaId']);
      }
      
      $mediaTag = $this->getHelper()->media($media);
  
      if (!empty($vars['width']) || !empty($vars['height']))
      {
        $mediaTag->size(dmArray::get($vars, 'width'), dmArray::get($vars, 'height'));
      }
    }
    else
    {
      $media    = null;
      $mediaTag = null;
    }
  
    $vars['media'] = $media;
    $vars['mediaTag'] = $mediaTag;

    return $vars;
  }

  protected function doRender()
  {
    if ($this->isCachable() && $cache = $this->getCache())
    {
      return $cache;
    }
    
    $vars = $this->getViewVars();
    
    if (!$vars['mediaTag'])
    {
      $html = '';
    }
    else
    {
      $html = $vars['mediaTag']->render();
    }
    
    if ($this->isCachable())
    {
      $this->setCache($html);
    }
    
    return $html;
  }
  
  protected function doRenderForIndex()
  {
    return $this->compiledVars['legend'];
  }
}