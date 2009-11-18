<?php

class dmWidgetContentMediaView extends dmWidgetPluginView
{
  
  public function configure()
  {
    parent::configure();

    $this->addRequiredVar(array('mediaId', 'method'));
  }

  protected function filterViewVars(array $vars = array())
  {
    $vars = parent::filterViewVars($vars);
    
    if (!empty($vars['mediaId']) || $this->isRequiredVar('mediaId'))
    {
      $media = dmDb::table('DmMedia')->findOneByIdWithFolder($vars['mediaId']);
      
      $mediaTag = dmMediaTag::build($media);
  
      if (!empty($vars['width']) || !empty($vars['height']))
      {
        $mediaTag->size(dmArray::get($vars, 'width'), dmArray::get($vars, 'height'));
      }
  
      $mediaTag->method($vars['method']);
  
      if ($vars['method'] === 'fit')
      {
        $mediaTag->background($vars['background']);
      }
      
      if ($vars['legend'])
      {
        $mediaTag->alt($vars['legend']);
      }
    }
    else
    {
      $media = null;
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
      $media = $vars['mediaTag'];
      
      if ($vars['legend'])
      {
        $vars['mediaTag']->alt($vars['legend']);
      }
      
      if ($vars['cssClass'])
      {
        $media->addClass($vars['cssClass']);
      }
      
      $html = $media->render();
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