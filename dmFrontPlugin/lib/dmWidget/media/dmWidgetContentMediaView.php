<?php

class dmWidgetContentMediaView extends dmWidgetPluginView
{
  public function configure()
  {
    parent::configure();

    $this->addRequiredVar(array('mediaId', 'method'));
  }

  public function getViewVars($vars = array())
  {
  	$vars = parent::getViewVars($vars);
  	
  	$media = dmDb::query('DmMedia m, m.Folder f')
  	->where('m.id = ?', $vars['mediaId'])
  	->fetchOne();

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

  	$vars['media'] = $mediaTag;

    return $vars;
  }

}