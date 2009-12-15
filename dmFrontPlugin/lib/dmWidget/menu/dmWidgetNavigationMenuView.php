<?php

class dmWidgetNavigationMenuView extends dmWidgetPluginView
{
  protected
  $isIndexable = false;

  public function configure()
  {
    parent::configure();
    
    $this->addRequiredVar('elements');
  }
  
  
  protected function doRender()
  {
    $vars = $this->getViewVars();
    
    $html = '<ul class="dm_menu_elements">';
    
    foreach($vars['elements'] as $element)
    {
      $link = $this->context->getHelper()->£link($element['source']);
      
      if (!empty($element['text']))
      {
        $link->text($element['text']);
      }
      
      if (!empty($element['attr']))
      {
        $link->set($element['attr']);
      }
      
      $html .= sprintf('<li class="dm_menu_element">%s</li>', $link->render());
    }
    
    $html .= '</ul>';
  }

}