<?php

class dmWidgetContentLinkView extends dmWidgetPluginView
{

  public function configure()
  {
    parent::configure();

    $this->addRequiredVar(array('href'));
  }
  
  protected function filterViewVars(array $vars = array())
  {
    $vars = parent::filterViewVars($vars);
    
    $vars['text'] = nl2br($vars['text']);
    
    return $vars;
  }

  protected function doRender()
  {
    if ($this->isCachable() && $cache = $this->getCache())
    {
      return $cache;
    }
    
    $vars = $this->getViewVars();
    
    $link = $this->getHelper()->link($vars['href']);

    if($vars['text'])
    {
      $link->text($vars['text']);
    }
    
    if($vars['title'])
    {
      $link->title($vars['title']);
    }
    
    if (isset($vars['target']) && $vars['target'] != '_self') $link->target($vars['target']);
    
    $html = $link->render();
    
    if ($this->isCachable())
    {
      $this->setCache($html);
    }
    
    return $html;
  }
  
  public function doRenderForIndex()
  {
    return $this->compiledVars['text'].' '.$this->compiledVars['title'];
  }
}