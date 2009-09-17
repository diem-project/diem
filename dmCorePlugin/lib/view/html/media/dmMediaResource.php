<?php

class dmMediaResource
{
  protected static
  $relativeUrlRoot,
  $culture,
  $theme;
  
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
	      
	      // allow culture variable in source
	      if (strpos($source, '%culture%') !== false)
	      {
	        $source = str_replace('%culture%', self::$culture, $source);
	      }
	
	      /*
	       * Server full path
	       */
	      if(strpos($source, sfConfig::get('sf_web_dir')) === 0)
	      {
	      	$this->pathFromWebDir = self::$relativeUrlRoot.str_replace(sfConfig::get('sf_web_dir'), '', $source);
	      }
	      // Web path ( ex: /swf/file.swf )
	      elseif(strncmp($source, '/', 1) === 0)
				{
					$this->pathFromWebDir = self::$relativeUrlRoot.$source;
				}
				// dm asset ( ex: dmFront/images/file.png )
				elseif(strncmp($source, 'dm', 2) === 0)
	      {
	      	$type = preg_replace('|^dm(\w+)/.+$|', '$1', $source);
	      	$realSource = str_replace('dm'.ucfirst($type).'/', '', $source);
	        $this->pathFromWebDir = self::$relativeUrlRoot.'/'.sfConfig::get('dm_'.$type.'_asset').'/'.$realSource;
	      }
	      // theme asset ( ex: images/file.png and file.png will both result to /myTheme/images/file.png )
	      else
	      {
	        // and now some magic to allow to use "images/file.png" writing only "file.png"
	        if (strncmp($source, 'images/', 7) !== 0)
	        {
	          if (file_exists(self::$theme->getFullPath($source)))
	          {
	            $this->pathFromWebDir = self::$theme->getWebPath($source);
	          }
	          elseif (file_exists(self::$theme->getFullPath('images/'.$source)))
	          {
	            $this->pathFromWebDir = self::$theme->getWebPath('images/'.$source);
	          }
	          else
	          {
	            $this->pathFromWebDir = self::$theme->getWebPath($source);
	          }
	        }
	        else
	        {
	          $this->pathFromWebDir = self::$theme->getWebPath($source);
	        }
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
		$this->source         = $media;
    $this->type           = self::MEDIA;
    $this->pathFromWebDir = $media->getwebPath();
    $this->mime           = $this->getSimpleMime($media->get('mime'));
	}

	protected function getSimpleMime($mime)
	{
    return substr($mime, 0, strpos($mime, '/'));
	}
  
  public static function setRelativeUrlRoot($relativeUrlRoot)
  {
    self::$relativeUrlRoot = $relativeUrlRoot;
  }
  
  public static function getRelativeUrlRoot()
  {
    return self::$relativeUrlRoot;
  }
  
  public static function setTheme(dmTheme $theme)
  {
    self::$theme = $theme;
  }
  
  public static function getTheme()
  {
    return self::$theme;
  }
  
  public static function setCulture($culture)
  {
    self::$culture = $culture;
  }
  
  public static function getCulture()
  {
    return self::$culture;
  }
	
}