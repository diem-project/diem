<?php

class dmMediaResource
{
  protected
    $theme,
    $culture,
    $requestContext,
    $source,
    $type,
    $mime,
    $pathFromWebDir;

  const
  MEDIA = 'media',
  FILE  = 'file',

  IMAGE = 'image',
  VIDEO = 'video',
  AUDIO = 'audio',
  FLASH = 'flash';

  public function __construct(dmTheme $theme, $culture, array $requestContext)
  {
    $this->theme           = $theme;
    $this->culture         = $culture;
    $this->requestContext  = $requestContext;
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
      throw new dmException('Can not build media from empty resource');
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
          $source = str_replace('%culture%', $this->culture, $source);
        }
  
        /*
         * Server full path
         */
        if(strpos($source, sfConfig::get('sf_web_dir')) === 0)
        {
          $this->pathFromWebDir = $this->requestContext['relative_url_root'].str_replace(sfConfig::get('sf_web_dir'), '', $source);
        }
        // Web path ( ex: /swf/file.swf )
        elseif(strncmp($source, '/', 1) === 0)
        {
          $this->pathFromWebDir = $this->requestContext['relative_url_root'].$source;
        }
        // dm asset ( ex: dmFront/images/file.png )
        elseif(strncmp($source, 'dm', 2) === 0)
        {
          $type = preg_replace('|^dm(\w+)/.+$|', '$1', $source);
          $realSource = str_replace('dm'.ucfirst($type).'/', '', $source);
          $this->pathFromWebDir = $this->requestContext['relative_url_root'].'/'.sfConfig::get('dm_'.$type.'_asset').'/'.$realSource;
        }
        // theme asset ( ex: images/file.png and file.png will both result to /myTheme/images/file.png )
        else
        {
          // and now some magic to allow to use "images/file.png" writing only "file.png"
          if (strncmp($source, 'images/', 7) !== 0 && file_exists($this->theme->getFullPath('images/'.$source)) && !file_exists($this->theme->getFullPath($source)))
          {
            $this->pathFromWebDir = $this->theme->getWebPath('images/'.$source);
          }
          else
          {
            $this->pathFromWebDir = $this->theme->getWebPath($source);
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
      throw new dmException('Bad media : '.$source);
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
  
  
}