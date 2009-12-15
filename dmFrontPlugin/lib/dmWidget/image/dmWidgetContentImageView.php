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
  
}