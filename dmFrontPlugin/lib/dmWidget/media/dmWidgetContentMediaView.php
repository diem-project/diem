<?php

class dmWidgetContentMediaView extends dmWidgetPluginView
{
	
  public function configure()
  {
    parent::configure();

    $this->addRequiredVar(array('mediaId', 'method'));
  }

  public function getViewVars(array $vars = array())
  {
  	$vars = parent::getViewVars($vars);
  	
  	if (!empty($vars['mediaId']) || $this->isRequiredVar('mediaId'))
  	{
	  	$media = dmMediaTag::build(dmDb::table('DmMedia')->findOneByIdWithFolder($vars['mediaId']));
	
			if (!empty($vars['width']) || !empty($vars['height']))
			{
			  $media->size(dmArray::get($vars, 'width'), dmArray::get($vars, 'height'));
			}
	
			$media->method($vars['method']);
	
			if ($vars['method'] === 'fit')
			{
				$media->background($vars['background']);
			}
  	}
  	else
  	{
  		$media = null;
  	}
  
    $vars['media'] = $media;

    return $vars;
  }

}