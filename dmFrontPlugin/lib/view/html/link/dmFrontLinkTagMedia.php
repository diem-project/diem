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
  	if (isset($this['text']))
  	{
  		return $this['text'];
  	}

  	return $this->media->file;
  }

}