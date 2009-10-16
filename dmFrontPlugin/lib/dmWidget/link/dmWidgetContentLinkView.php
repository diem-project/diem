<?php

class dmWidgetContentLinkView extends dmWidgetPluginView
{

  public function configure()
  {
    parent::configure();

    $this->addRequiredVar(array('href'));
  }
  
  public function getViewVars(array $vars = array())
  {
    $vars = parent::getViewVars($vars);
    
    $vars['text'] = nl2br($vars['text']);
    
    return $vars;
  }

  protected function doRender(array $vars)
  {
    $link = dmFrontLinkTag::build($vars['href']);

    if($vars['text'])
    {
      $link->text($vars['text']);
    }
    
    if($vars['title'])
    {
      $link->title($vars['title']);
    }
    
    if($vars['cssClass'])
    {
      $link->addClass($vars['cssClass']);
    }
    
    return $link->render();
  }
  
  public function doRenderForIndex(array $vars)
  {
    return $vars['text'].' '.$vars['title'];
  }
}