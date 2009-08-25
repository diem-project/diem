<?php

class dmMediaResource
{

	protected
	  $source,
	  $type,
	  $mime,
	  $pathFromWebDir;

	const
	MEDIA = 'media',
  FILE  = 'file',
	ERROR = 'error',

	IMAGE = 'image',
	VIDEO = 'video',
	AUDIO = 'audio',
	FLASH = 'flash';

	public function __construct($source)
	{
		$this->initialize($source);
	}

	public function __toString()
	{
		return (string) $this->source;
	}

	public function getSource()
	{
		return $this->source;
	}

	public function getType()
	{
		return $this->type;
	}

	public function isType($type)
	{
		return $this->type === $type;
	}

	public function getMime()
	{
		return $this->mime;
	}

	public function getPathFromWebDir()
	{
		return $this->pathFromWebDir;
	}

	public function getFullPath()
	{
		switch($this->getType())
		{
			case MEDIA:
				return $this->source->getFullPath();
			case FILE:
				return dmOs::join(sfConfig::get('sf_web_dir'), $this->getPathFromWebDir());
		}
	}

	public function initialize($source)
	{
    $this->source = $source;
    
		if (empty($source))
		{
			$this->type = self::ERROR;
		}
		elseif (is_string($source))
		{
		  if (strncmp($source, 'media:', 6) === 0)
      {
        if ($media = dmDb::table('DmMedia')->findOneByIdWithFolder(preg_replace('|^media:(\d+).*|', '$1', $source)))
        {
          $this->fromMedia($media);
        }
        else
        {
          throw new dmException(sprintf('%s is not a valid media resource', $source));
        }
      }
      else
      {
	      $this->type = self::FILE;
	
	      /*
	       * Server full path
	       */
	      if(strpos($source, sfConfig::get('sf_web_dir')) === 0)
	      {
	      	$source = dm::getRequest()->getRelativeUrlRoot().'/'.str_replace(sfConfig::get('sf_web_dir'), '', $source);
	      }
	
	      if(strncmp($source, "/", 1) === 0)
				{
					$this->pathFromWebDir = dm::getRequest()->getRelativeUrlRoot().$source;
				}
				elseif(strncmp($source, "dm", 2) === 0)
	      {
	      	$dmPlugin = dmString::modulize(preg_replace('|^dm([\w\d]+)/.+$|', '$1', $source));
	      	$realSource = str_replace('dm'.ucfirst($dmPlugin).'/', '', $source);
	        $this->pathFromWebDir = dm::getRequest()->getRelativeUrlRoot().'/'.sfConfig::get('dm_'.$dmPlugin.'_asset').'/images/'.$realSource;
	      }
	      else
	      {
	        $this->pathFromWebDir = dm::getUser()->getTheme()->getWebPath('images/'.$source);
	      }
	
	      $this->mime = $this->getSimpleMime(dmOs::getFileMime($source));
      }
		}
		elseif($source instanceof DmMedia)
		{
			$this->fromMedia($source);
		}
		else
		{
      $this->type = self::ERROR;
		}
	}
	
	protected function fromMedia(DmMedia $media)
	{
		$this->source = $media;
    $this->type = self::MEDIA;
    $this->pathFromWebDir = $media->webPath;
    $this->mime = $this->getSimpleMime($media->mime);
	}

	protected function getSimpleMime($mime)
	{
    return substr($mime, 0, strpos($mime, '/'));
	}
}