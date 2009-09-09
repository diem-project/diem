<?php

class dmMediaImageTag extends dmMediaTag
{

  /*
   * available methods for thumbnail creation
   */
  protected static
  $methods = array('fit', 'scale', 'inflate', 'left', 'right', 'top', 'bottom', 'center'),
  $filters = array('greyscale');

  public function __construct($resource)
  {
    parent::__construct($resource);

    $this->method(dmConfig::get('image_resize_method', 'center'));
    $this->set('quality', dmConfig::get('image_quality', 92));
    $this->set('background', null);
  }

  public function method($method)
  {
  	if (!in_array($method, self::getMethods()))
  	{
  		throw new dmException(sprintf('%s is not a valid method. These are : %s',
  		  $method,
  		  implode(', ', self::$methods)
  		));
  	}
    return $this->set('method', (string)$method);
  }

  public function quality($v)
  {
    return $this->set('quality', (int)$v);
  }

  public function background($v)
  {
    return $this->set('background', (string)$v);
  }

  public function alt($v)
  {
    return $this->set('alt', (string)$v);
  }

  public function filter($filterName, $filterOptions = array())
  {
    if (!in_array($filterName, self::$filters))
    {
      throw new dmMediaImageException(sprintf('%s is not a valid filter. These are : %s',
        filterName,
        implode(', ', self::$filters)
      ));
    }
    return $this->set('filter', (string)$filterName);
  }
  
	public function render()
	{
    $tag = '<img'.$this->getHtmlAttributes().' />';

    return $tag;
	}

  protected function getNonHtmlAttributes()
  {
    return array_merge(
      parent::getNonHtmlAttributes(),
      array('method', 'quality', 'background')
    );
  }

  protected function prepareAttributesForHtml(array $attributes)
  {
    $attributes = parent::prepareAttributesForHtml($attributes);

    if(!isset($attributes['alt']))
    {
    	$attributes['alt'] = '';
    }

    if ($this->resource->isType(dmMediaResource::MEDIA))
    {
      $attributes = $this->prepareMediaAttributes($attributes);
    }

    return $attributes;
  }

  protected function prepareMediaAttributes(array $attributes)
  {
    if ($this->hasSize())
    {
    	$mediaFullPath = $this->getResizedMediaFullPath($attributes);

      $attributes['src'] = dm::getRequest()->getRelativeUrlRoot().str_replace(sfConfig::get('sf_web_dir'), '', $mediaFullPath);
      /*
       * When thumb method is scale,
       * html width and height
       * should be different than request width and height
       */
      if ($attributes['method'] === 'scale' )
      {
        $infos = getimagesize($mediaFullPath);
        $attributes['width'] = $infos[0];
        $attributes['height'] = $infos[1];
      }
    }

    return $attributes;
  }

  protected function getResizedMediaFullPath(array $attributes)
  {
  	$media = $this->resource->getSource();

  	$attributes['background'] = trim($attributes['background'], '#');

      if (!in_array($attributes['method'], self::getMethods()))
      {
      	$attributes['method'] = dmConfig::get('image_resize_method', 'center');
      	// throw new dmException($attributes['method'].' is not a valid resizer method. These are '.implode(', ', self::getMethods()));
      }

      if(!dmFilesystem::get()->mkdir($thumbDir = dmOs::join($media->Folder->fullPath, '.thumbs')))
      {
      	dm::getUser()->logAlert(dm::getI18n()->__('Thumbnails can not be created in %1%', array('%1%' => $media->Folder->relPath)), false);
        return $media->fullPath;
      }
      
      $filter = dmArray::get($attributes, 'filter');

      $thumbBasename = sprintf('%sx%s-%s_%s_%s_%d_%s',
        $attributes['width'],
        $attributes['height'],
        $attributes['method'] === 'fit' ? 'fit'.$attributes['background'] : $attributes['method'],
        $filter,
        $attributes['quality'],
        $media->getLittleMTime(),
        $media->file
      );

      $thumbPath = dmOs::join($thumbDir, $thumbBasename);

      if (!file_exists($thumbPath))
      {
      	dmDebug::log('Recreate thumb for media '.$media);

        $image = $media->getImage();
        
        $image->setQuality($attributes['quality']);

        $image->thumbnail($attributes['width'], $attributes['height'], $attributes['method'], $attributes['background'] ? '#'.$attributes['background'] : null);

        if ($filter)
        {
        	try
        	{
          	$image->$filter();
        	}
        	catch(sfImageTransformException $e)
        	{
        		if (sfConfig::get('sf_debug'))
        		{
        			throw $e;
        		}
        	}
        }
        
        $image->saveAs($thumbPath, $media->mime);

	      if (!file_exists($thumbPath))
	      {
	        throw new dmException($thumbPath.' cannot be created');
	      }
      }

    return $thumbPath;
  }

  public static function getMethods()
  {
  	return self::$methods;
  }

}