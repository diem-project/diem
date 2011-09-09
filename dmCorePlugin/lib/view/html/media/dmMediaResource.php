<?php

class dmMediaResource
{
  protected
  $mimeTypeResolver,
  $theme,
  $culture,
  $requestContext,
  $source,
  $type,
  $mime,
  $pathFromWebDir,
  $remotePath;

  const
  MEDIA   = 'media',
  FILE    = 'file',
  REMOTE  = 'remote';

  public function __construct(dmMimeTypeResolver $mimeTypeResolver, dmTheme $theme, $culture, array $requestContext)
  {
    $this->mimeTypeResolver = $mimeTypeResolver;
    $this->theme            = $theme;
    $this->culture          = $culture;
    $this->requestContext   = $requestContext;
  }

  public function getSource()
  {
    return $this->source;
  }
  
  public function __toString()
  {
    return (string) $this->source;
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
  
  public function getWebPath($absolute = false)
  {
    if($this->type === self::REMOTE)
    {
      $webPath = $this->remotePath;
    }
    else
    {
    	if(!$absolute)
    	{
      	$webPath = $this->requestContext['relative_url_root'].$this->pathFromWebDir;
    	}
    	else
    	{
    		$webPath = $this->requestContext['absolute_url_root'].$this->pathFromWebDir;
    	}
    }
    
    return $webPath;
  }

  public function getFullPath()
  {
    switch($this->getType())
    {
      case self::MEDIA:
        return $this->source->getFullPath();
      case self::FILE:
        return dmOs::join(sfConfig::get('sf_web_dir'), $this->getPathFromWebDir());
      case self::REMOTE:
        return $this->remotePath;
    }
  }

  public function initialize($source, $isDefault = false)
  {
    $this->source   = $source;
    
    if (empty($source))
    {
    }
    elseif (is_string($source))
    {
      if (strncmp($source, 'media:', 6) === 0)
      {
        $mediaId = preg_replace('|^media:(\d+).*|', '$1', $source);
        
        if ($media = dmDb::table('DmMedia')->findOneByIdWithFolder($mediaId))
        {
          $this->fromMedia($media);
        }
        else
        {
          throw new dmException(sprintf('%s is not a valid media resource. The media with id %s does not exist', $source, $mediaId));
        }
      }
      elseif (0 === strncmp($source, 'http://', 7))
      {
        $this->type = self::REMOTE;
        $this->remotePath = $source;
  
        $this->mime = $this->mimeTypeResolver->getGroupByFilename(dmString::getBaseFromUrl($source));
        $this->mime = $this->mime ? $this->mime : 'image';
      }
      else
      {
        $this->type = self::FILE;
        
        // allow culture variable in source
        if (strpos($source, '%culture%') !== false)
        {
          $source = str_replace('%culture%', $this->culture, $source);
        }
  
        /*
         * Server full path
         */
        if(strpos($source, sfConfig::get('sf_web_dir')) === 0)
        {
          $this->pathFromWebDir = str_replace(sfConfig::get('sf_web_dir'), '', $source);
        }
        // Web path ( ex: /swf/file.swf )
        elseif(strncmp($source, '/', 1) === 0)
        {
          $this->pathFromWebDir = $source;
        }
        // dm asset ( ex: dmFront/images/file.png )
        elseif(strncmp($source, 'dm', 2) === 0)
        {
          $type = preg_replace('|^dm(\w+)/.+$|', '$1', $source);
          $this->pathFromWebDir = '/'.sfConfig::get('dm_'.dmString::modulize($type).'_asset').'/'.str_replace('dm'.$type.'/', '', $source);
        }
        // theme asset ( ex: images/file.png and file.png will both result to /myTheme/images/file.png )
        else
        {
          // and now some magic to allow to use "images/file.png" writing only "file.png"
          if (strncmp($source, 'images/', 7) !== 0 && file_exists($this->theme->getFullPath('images/'.$source)) && !file_exists($this->theme->getFullPath($source)))
          {
            $this->pathFromWebDir = $this->theme->getPath('images/'.$source);
          }
          else
          {
            $this->pathFromWebDir = $this->theme->getPath($source);
          }
        }

        $this->mime = $this->mimeTypeResolver->getGroupByFilename(dmString::getBaseFromUrl($source));
      }
    }
    elseif($source instanceof DmMedia)
    {
      if ($source->isNew())
      {
      }
      else
      {
        $this->fromMedia($source);
      }
    }
    else
    {
      throw new dmException('Not a valid media source : '.$source);
    }
    
    if (!$this->getMime())
    {
      if($isDefault)
      {
        $this->pathFromWebDir = '';
        $this->mime = 'image';
      }
      else
      {
        $this->initialize(sfConfig::get('dm_media_default'), true);
      }
    }
    
    return $this;
  }
  
  protected function fromMedia(DmMedia $media)
  {
    $this->source         = $media;
    $this->type           = self::MEDIA;
    $this->pathFromWebDir = '/'.$media->getWebPath();
    $this->mime           = $this->getSimpleMime($media->get('mime'));
  }

  protected function getSimpleMime($mime)
  {
    return substr($mime, 0, strpos($mime, '/'));
  }
  
}