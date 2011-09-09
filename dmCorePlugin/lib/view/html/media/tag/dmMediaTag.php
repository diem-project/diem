<?php

abstract class dmMediaTag extends dmHtmlTag
{
  protected
  $resource,
  $context,
  $stylesheets = array(),
  $javascripts = array();

  public function __construct(dmMediaResource $resource, dmContext $context, array $options = array())
  {
    $this->resource         = $resource;
    $this->context          = $context;

    $this->addAttributeToRemove('absolute');
    $this->initialize($options);
  }
  
  public function getDefaultOptions()
  {
  	return array_merge(parent::getDefaultOptions(), array(
  		'absolute' 				=> false
  	));
  }
  
  public function absolute($bool)
  {
  	return $this->setOption('absolute', (boolean) $bool);
  }
  
  public function isAbsolute()
  {
  	return $this->getOption('absolute');
  }
  
  public function width($v)
  {
    return $this->setOption('width', max(0, (int)$v));
  }

  public function height($v)
  {
    return $this->setOption('height', max(0, (int)$v));
  }

  public function size($width, $height = null)
  {
    if (is_array($width))
    {
      list($width, $height) = $width;
    }

    return $this->width($width)->height($height);
  }

  public function getSrc($throwException = true)
  {
    if ($throwException)
    {
      return dmArray::get($this->prepareAttributesForHtml($this->options), 'src');
    }
    else
    {
      try
      {
        return dmArray::get($this->prepareAttributesForHtml($this->options), 'src');
      }
      catch(Exception $e)
      {
        return false;
      }
    }
  }

  public function getAbsoluteSrc($throwException = true)
  {
  	$this->absolute(true);
    return $this->getSrc($throwException);
  }

  public function getWidth()
  {
    return dmArray::get($this->prepareAttributesForHtml($this->options), 'width');
  }

  public function getHeight()
  {
    return dmArray::get($this->prepareAttributesForHtml($this->options), 'width');
  }

  protected function prepareAttributesForHtml(array $attributes)
  {
    $attributes = parent::prepareAttributesForHtml($attributes);

    $attributes['src'] = $this->resource->getWebPath($this->options['absolute']);

    return $attributes;
  }

  protected function hasSize()
  {
    return !(empty($this->options['width']) && empty($this->options['height']));
  }

  public function quality($val)
  {
    // override me
  }

  protected function addJavascript($keys)
  {
    $this->javascripts = array_merge($this->javascripts, (array) $keys);
  }

  public function getJavascripts()
  {
    return $this->javascripts;
  }

  protected function addStylesheet($keys)
  {
    $this->stylesheets = array_merge($this->stylesheets, (array) $keys);
  }

  public function getStylesheets()
  {
    return $this->stylesheets;
  }
}