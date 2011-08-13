<?php

abstract class dmBaseLinkTag extends dmHtmlTag
{
  protected
  $resource;
  
  protected function initialize(array $options = array())
  {
    parent::initialize($options);
    
    $this->addAttributeToRemove(array('text', 'anchor', 'current_class', 'parent_class', 'current_span', 'use_beaf', 'http_secure'));
    
    $this->addEmptyAttributeToRemove(array('target', 'title'));
    
    $this->addClass('link');
  }

  public function getDefaultOptions()
  {
    return array_merge(parent::getDefaultOptions(), array(
      'current_class' => 'dm_current',
      'parent_class'  => 'dm_parent',
      'current_span'  => false,
      'use_beaf'      => false,
    	'http_secure' 	=> false
    ));
  }
  
  public function isHttpSecure()
  {
  	return (boolean) $this->options['http_secure'];
  }
  
  public function httpSecure($secure = true)
  {
  	$this->options['http_secure'] = (boolean) $secure;
  	
  	return $this;
  }

  public function isCurrent()
  {
    return false;
  }

  public function isParent()
  {
    return false;
  }
  
  /**
   * @return string baseHref the href without query string
   */
  abstract protected function getBaseHref();
    
  /**
   * Set text
   * @return dmLinkTag $this
   */
  public function text($v)
  {
    return $this->setOption('text', (string) $v);
  }

  /**
   * Set title
   * @return dmLinkTag $this
   */
  public function title($v)
  {
    return $this->setOption('title', (string) $v);
  }

  /**
   * Set text and title
   * @return dmLinkTag $this
   */
  public function textTitle($v)
  {
    return $this->text($v)->title($v);
  }

  /**
   * Set link target
   * @return dmLinkTag $this
   */
  public function target($v)
  {
    if (in_array($v, array('blank', 'parent', 'self', 'top')))
    {
      $v = '_'.$v;
    }

    return $this->setOption('target', strtolower($v));
  }

  /**
   * Add an anchor
   * @return dmLinkTag $this
   */
  public function anchor($v)
  {
    return $this->setOption('anchor', trim((string) $v, '#'));
  }

  /**
   * Add a request parameter
   * @return dmLinkTag $this
   */
  public function param($key, $value)
  {
    return $this->params(array($key => $value));
  }

  /**
   * Add request parameters
   * @return dmLinkTag $this
   */
  public function params(array $params)
  {
    return $this->setOption('params', array_merge($this->get('params', array()), $params));
  }

  /**
   * Whether to display current links with span tag
   * @return dmLinkTag $this
   */
  public function currentSpan($bool)
  {
    return $this->setOption('current_span', (bool) $bool);
  }

  /**
   * Sets the current css class
   * @return dmLinkTag $this
   */
  public function currentClass($class)
  {
    return $this->setOption('current_class', (string) $class);
  }

  /**
   * Sets the parent css class
   * @return dmLinkTag $this
   */
  public function parentClass($class)
  {
    return $this->setOption('parent_class', (string) $class);
  }

  public function render()
  {
    return $this->doRender('a', $this->getHtmlAttributes(), $this->renderText());
  }

  protected function doRender($tag, $htmlAttributes, $text)
  {
    if($this->options['use_beaf'])
    {
      return $this->doRenderBeaf($tag, $htmlAttributes, $text);
    }
    
    return '<'.$tag.$htmlAttributes.'>'.$text.'</'.$tag.'>';
  }

  protected function doRenderBeaf($tag, $htmlAttributes, $text)
  {
    if(in_array('beafh', $this->options['class']) || in_array('beafv', $this->options['class']))
    {
      return '<'.$tag.$htmlAttributes.'><span class="beafore"></span><span class="beafin">'.$text.'</span><span class="beafter"></span></span></a>';
    }

    return '<'.$tag.$htmlAttributes.'>'.$text.'</'.$tag.'>';
  }

  protected function prepareAttributesForHtml(array $attributes)
  {
    $attributes = parent::prepareAttributesForHtml($attributes);

    $attributes['href'] = $this->getBaseHref();

    if (array_key_exists('params', $attributes))
    {
      if (!empty($attributes['params']))
      {
        $attributes['href'] = $this->buildUrl(
        dmString::getBaseFromUrl($attributes['href']),
        array_merge(dmString::getDataFromUrl($attributes['href']), $attributes['params'])
        );

        /*
         * if last href char is a =, remove it
         * fixes http://github.com/diem-project/diem/issues/#issue/6
         */
        if('=' === substr($attributes['href'], -1))
        {
          $attributes['href'] = substr($attributes['href'], 0, strlen($attributes['href']) - 1);
        }
      }
      
      unset($attributes['params']);
    }
    
    if (isset($attributes['anchor']))
    {
      $attributes['href'] .= '#'.$attributes['anchor'];
    }

    if(!empty($attributes['class']) && in_array('nofollow', $attributes['class']))
    {
      $attributes['nofollow'] = true;
    }

    // makes unit testing easier
    ksort($attributes);
    
    return $attributes;
  }

  public function getHref()
  {
    return dmArray::get($this->prepareAttributesForHtml($this->options), 'href');
  }
  
  public function getText()
  {
    return $this->renderText();
  }

  protected function renderText()
  {
    return $this->options['text'];
  }

  protected function buildUrl($base, array $data = array())
  {
    return $base.'?'.http_build_query($data, null, '&');
  }
  
}