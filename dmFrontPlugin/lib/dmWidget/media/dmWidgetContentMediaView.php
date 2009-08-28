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

  protected function doRender(array $vars)
  {
  	if (!$vars['mediaTag'])
  	{
  		$html = '';
  	}
  	else
  	{
  	  $html = $vars['mediaTag']->alt($vars['legend']);
  	}
  	
  	return $html;
  }
  
  public function toIndexableString(array $vars)
  {
    return $vars['legend'];
  }
  
}