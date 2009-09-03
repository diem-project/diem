<?php

class dmWidgetNavigationMenuView extends dmWidgetPluginView
{

	public function configure()
	{
    parent::configure();
		
    $this->addRequiredVar('elements');
	}
  
  public function toIndexableString(array $vars)
  {
    return '';
  }
  
  protected function doRender(array $vars)
  {
    $html = '<ul class="dm_menu_elements">';
    
    foreach($vars['elements'] as $element)
    {
    	$link = dmFrontLinkTag::build($element['source']);
      
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