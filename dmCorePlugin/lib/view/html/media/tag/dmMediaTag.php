<?php

abstract class dmMediaTag extends dmHtmlTag
{
  protected
  $resource,
  $theme,
  $culture,
  $requestContext;

  public function __construct(dmMediaResource $resource, dmTheme $theme, $culture, array $requestContext, array $options = array())
  {
    $this->resource         = $resource;
    $this->theme            = $theme;
    $this->culture          = $culture;
    $this->requestContext   = $requestContext;
    
    $this->initialize($options);
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

  public function getSrc()
  {
    return dmArray::get($this->prepareAttributesForHtml($this->options), 'src');
  }

  protected function prepareAttributesForHtml(array $attributes)
  {
    $attributes = parent::prepareAttributesForHtml($attributes);

    $attributes['src'] = $this->resource->getWebPath();

    return $attributes;
  }

  protected function hasSize()
  {
    return !(empty($this->options['width']) && empty($this->options['height']));
  }

}