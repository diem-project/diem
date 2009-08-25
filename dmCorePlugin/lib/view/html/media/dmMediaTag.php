<?php

abstract class dmMediaTag extends dmHtmlTag
{
	protected
	$attributesToRemove = array('resource');

	public static function build($source)
  {
    $resource = new dmMediaResource($source);

    $mediaClass = 'dmMedia'.dmString::camelize($resource->getMime()).'Tag';

    if (!class_exists($mediaClass) || $mediaClass === 'dmMediaTag')
    {
    	throw new dmException(sprintf(
    	  'Unrecognized media %s with mime type %s, class=%s',
    	  $source,
    	  $resource->getMime(),
    	  $mediaClass
    	));
    }

    return new $mediaClass($resource);
  }

  public function __construct($resource)
  {
    $this->resource = $resource;
  }

  public function width($v)
  {
    return $this->set('width', max(0, (int)$v));
  }

  public function height($v)
  {
    return $this->set('height', max(0, (int)$v));
  }

  public function size($width, $height = null)
  {
    if (is_array($width))
    {
      list($width, $height) = $width;
    }

    return $this->width($width)->height($height);
  }

  public function src()
  {
  	try
  	{
      return dmArray::get($this->prepareAttributesForHtml($this->options), 'src');
  	}
  	catch(Exception $e)
  	{
  		dmDebug::log(sprintf('Error while getting image src for %s : %s', $this->resource, $e->getMessage()));
  	}
  }

  protected function prepareAttributesForHtml(array $attributes)
  {
    $attributes = parent::prepareAttributesForHtml($attributes);

    $attributes['src'] = $this->resource->getPathFromWebDir();

    return $attributes;
  }

  protected function hasSize()
  {
    return !(empty($this['width']) && empty($this['height']));
  }

}