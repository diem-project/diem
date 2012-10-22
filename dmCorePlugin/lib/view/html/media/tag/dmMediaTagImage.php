<?php

class dmMediaTagImage extends dmMediaTag
{

  protected static
  $verifiedThumbDirs = array();

  public function initialize(array $options = array())
  {
    parent::initialize($options);

    $this->addAttributeToRemove(array('resize_method', 'resize_quality', 'background', 'filter', 'overlay'));
  }
  
  public function getDefaultOptions()
  {
    return array_merge(parent::getDefaultOptions(), array(
      'background' => null
    ));
  }
  
  public function htmlWidth($v)
  {
    return $this->setOption('html_width', strpos($v, '%') ? $v : max(0, (int)$v));
  }

  public function htmlHeight($v)
  {
    return $this->setOption('html_height', strpos($v, '%') ? $v : max(0, (int)$v));
  }

  public function htmlSize($width, $height = null)
  {
    if (is_array($width))
    {
      list($width, $height) = $width;
    }

    return $this->htmlWidth($width)->htmlHeight($height);
  }

  public function method($method)
  {
    if (!in_array($method, $this->getAvailableMethods()))
    {
      throw new dmException(sprintf('%s is not a valid method. These are : %s',
      $method,
      implode(', ', $this->getAvailableMethods())
      ));
    }

    return $this->setOption('resize_method', $method);
  }

  public function quality($v)
  {
    return $this->setOption('resize_quality', (int) $v);
  }
  
  public function overlay(dmMediaTagImage $image, $position = 'center')
  {
    return $this->setOption('overlay', array(
      'image' => $image,
      'position' => $position
    ));
  }

  public function background($v)
  {
    if (!$hexColor = dmString::hexColor($v))
    {
      throw new dmException(sprintf('%s is not a valid hexadecimal color', $v));
    }
    
    return $this->setOption('background', $hexColor);
  }

  public function alt($v)
  {
    return $this->setOption('alt', (string)$v);
  }

  public function filter($filterName, $filterOptions = array())
  {
    if (!in_array($filterName, $this->getAvailableFilters()))
    {
      throw new dmException(sprintf('%s is not a valid filter. These are : %s',
      $filterName,
      implode(', ', $this->getAvailableFilters())
      ));
    }
    
    return $this->setOption('filter', (string) $filterName);
  }

  public function render()
  {
    if (!$this->resource->getSource())
    {
      if(sfConfig::get('sf_logging_enabled'))
      {
        $this->context->getLogger()->warning('Skipped empty media rendering');
      }
      
      return $this->renderDefault();
    }
    
    $tag = '<img'.$this->getHtmlAttributes().' />';

    return $tag;
  }
  
  public function renderDefault()
  {
    return '';
  }
  
  public function getRealSize()
  {
    $infos = getimagesize($this->getServerFullPath());
    
    return array($infos[0], $infos[1]);
  }
  
  public function getServerFullPath()
  {
    if ($this->hasSize() && !sfConfig::get('dm_search_populating') && $this->resource->getSource() instanceof DmMedia)
    {
      $fullPath = $this->getResizedMediaFullPath($this->options);
    }
    else
    {
      $fullPath = $this->resource->getFullPath();
    }
    
    return $fullPath;
  }

  protected function prepareAttributesForHtml(array $attributes)
  {
    $attributes = parent::prepareAttributesForHtml($attributes);

    if ($this->resource->isType(dmMediaResource::MEDIA))
    {
      $attributes = $this->prepareMediaAttributes($attributes);
    }
    elseif(!$this->hasSize() && !$this->get('html_width') && !$this->get('html_height') && $this->resource->isType(dmMediaResource::FILE))
    {
      if (@$infos = getimagesize($this->resource->getFullPath()))
      {
        $attributes['width'] = $infos[0];
        $attributes['height'] = $infos[1];
      }
    }

    if(!isset($attributes['alt']) && sfConfig::get('dm_accessibility_image_empty_alts', true))
    {
      $attributes['alt'] = '';
    }
  
    if (isset($attributes['html_width']))
    {
      $attributes['width'] = $attributes['html_width'];
      unset($attributes['html_width']);
    }
    if (isset($attributes['html_height']))
    {
      $attributes['height'] = $attributes['html_height'];
      unset($attributes['html_height']);
    }

    // helps unit testing
    ksort($attributes);
    
    return $attributes;
  }

  protected function prepareMediaAttributes(array $attributes)
  {
    $media = $this->resource->getSource();
    
    if (!$media->checkFileExists())
    {
      throw new dmException('The media file does not exist : '.$media->relPath);
    }
    
    $validImage = true;
    
    if ($this->hasSize() && !sfConfig::get('dm_search_populating'))
    {
      try
      {
        $mediaFullPath = $this->getResizedMediaFullPath($attributes);
      }
      catch(Exception $e)
      {
        $this->context->getLogger()->err($e->getMessage());
        
        if (sfConfig::get('dm_debug'))
        {
          throw $e;
        }
        
        $validImage = false;
        $mediaFullPath = $media->getFullPath();
      }
    }
    else
    {
      $mediaFullPath = $media->getFullPath();
    }
    
    if (@$infos = getimagesize($mediaFullPath))
    {
      if ($validImage)
      {
        $attributes['width'] = $infos[0];
        $attributes['height'] = $infos[1];
      }
    }
    else
    {
      throw new dmException('The image is not readable : '.dmProject::unRootify($mediaFullPath));
    }

    $attributes['src'] = (!$this->options['absolute'] ? $this->context->getRequest()->getRelativeUrlRoot() : $this->context->getRequest()->getAbsoluteUrlRoot()) . 
    											str_replace(dmOs::normalize(sfConfig::get('sf_web_dir')), '', dmOs::normalize($mediaFullPath));
    
    if(!isset($attributes['alt']) && $media->get('legend'))
    {
      $attributes['alt'] = $media->get('legend');
    }

    return $attributes;
  }

  protected function getResizedMediaFullPath(array $attributes)
  {
    $media = $this->resource->getSource();
    
    if (!$media instanceof DmMedia)
    {
      throw new dmException('Can be used only if the source is a DmMedia instance');
    }

    if (empty($attributes['width']))
    {
      $attributes['width'] = $media->getWidth();
    }
    elseif (empty($attributes['height']))
    {
      $attributes['height'] = (int) ($media->getHeight() * ($attributes['width'] / $media->getWidth()));
    }

    $filter = dmArray::get($attributes, 'filter');
    $overlay = dmArray::get($attributes, 'overlay', array());
    
    /*
     * Nothing to change, return the original image
     */
    if ($attributes['width'] == $media->getWidth() && $attributes['height'] == $media->getHeight() && !$filter && !$overlay)
    {
      return $media->getFullPath();
    }

    if ($attributes['resize_method'] == 'fit')
    {
      $attributes['background'] = trim($attributes['background'], '#');
    }

    if (!in_array($attributes['resize_method'], $this->getAvailableMethods()))
    {
      throw new dmException(sprintf('%s is not a valid resize method. These are : %s', $attributes['resize_method'], implode(', ', $this->getAvailableMethods())));
    }

    if (!$thumbDir = dmArray::get(self::$verifiedThumbDirs, $media->get('dm_media_folder_id')))
    {
      $thumbDir = dmOs::join($media->get('Folder')->getFullPath(), '.thumbs');
      
      if(!@$this->context->getFilesystem()->mkdir($thumbDir))
      {
        throw new dmException('Thumbnails can not be created in '.$media->get('Folder')->getFullPath());
      }
      
      self::$verifiedThumbDirs[$media->get('dm_media_folder_id')] = $thumbDir;
    }

    $pathInfo = pathinfo($media->get('file'));
    
    $thumbRelPath = $pathInfo['filename'].'_'.substr(md5(implode('-', array(
      $attributes['width'],
      $attributes['height'],
      $attributes['resize_method'] === 'fit' ? 'fit'.$attributes['background'] : $attributes['resize_method'],
      $filter,
      implode(' ', $overlay),
      $attributes['resize_quality'],
      $media->getTimeHash()
    ))), -6).'.'.$pathInfo['extension'];

    $thumbPath = $thumbDir.'/'.$thumbRelPath;

    if (!file_exists($thumbPath))
    {
      $image = $media->getImage();

      $image->setQuality($attributes['resize_quality']);

      $image->thumbnail($attributes['width'], $attributes['height'], $attributes['resize_method'], !empty($attributes['background']) ? '#'.$attributes['background'] : null);
    
      if ($filter)
      {
        $image->$filter();
      }
    
      if (!empty($overlay))
      {
        $overlayPath = $overlay['image']->getServerFullPath();
        $type = $this->context->get('mime_type_resolver')->getByFilename($overlayPath);
        
        if ($type != 'image/png')
        {
          throw new dmException('Only png images can be used as overlay.');
        }
        
        $image->overlay(new sfImage($overlayPath, $type), $overlay['position']);
      }

      $image->saveAs($thumbPath, $media->get('mime'));

      if (!file_exists($thumbPath))
      {
        throw new dmException(dmProject::unRootify($thumbPath).' cannot be created');
      }
      else
      {
        $this->context->getFilesystem()->chmod($thumbPath, 0666);
      }
    }

    return $thumbPath;
  }

  public function getAvailableFilters()
  {
    return array('greyscale');
  }

  public function getAvailableMethods()
  {
    return array('fit', 'scale', 'inflate', 'left', 'right', 'top', 'bottom', 'center');
  }

}