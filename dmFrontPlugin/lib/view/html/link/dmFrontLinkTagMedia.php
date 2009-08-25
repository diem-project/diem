<?php

class dmFrontLinkTagMedia extends dmFrontLinkTag
{
	protected
	$media;

	protected function configure()
	{
		$this->media = $this->get('source');
	}
	
  protected function getBaseHref()
	{
		return dm::getRequest()->getAbsoluteUrlRoot().'/'.$this->media->webPath;
	}

  protected function renderName()
  {
  	if (isset($this['name']))
  	{
  		return $this['name'];
  	}

  	return $this->media->file;
  }

}