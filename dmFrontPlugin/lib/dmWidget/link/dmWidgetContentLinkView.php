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
		
		return $link->render();
	}
  
  public function toIndexableString(array $vars)
  {
    return implode(' ', $vars['text'], $vars['title']);
  }
}