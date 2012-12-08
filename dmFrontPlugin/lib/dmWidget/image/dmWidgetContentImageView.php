<?php

class dmWidgetContentImageView extends dmWidgetContentBaseMediaView
{
  
  public function configure()
  {
    parent::configure();

    $this->addRequiredVar('method');
  }

  protected function filterViewVars(array $vars = array())
  {
    $vars = parent::filterViewVars($vars);
    
    if ($vars['mediaTag'])
    {
      if ($vars['legend'])
      {
        $vars['mediaTag']->alt($vars['legend']);
      }
      
      $vars['mediaTag']->method($vars['method']);
  
      if ($vars['method'] === 'fit')
      {
        $vars['mediaTag']->background($vars['background']);
      }
      
      if ($quality = dmArray::get($vars, 'quality'))
      {
        $vars['mediaTag']->quality($quality);
      }
      
      if ($vars['legend'])
      {
        $vars['mediaTag']->alt($vars['legend']);
      }
    }

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

    if($link = dmArray::get($vars, 'link'))
    {
      $link = $this->getHelper()->link($link)->text($html);
      if (isset ($vars['target']) && $vars['target'] != '_self') $link->target($vars['target']);
      $html = $link->render();
    }

    if ($this->isCachable())
    {
      $this->setCache($html);
    }

    return $html;
  }
}