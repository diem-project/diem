<?php

abstract class dmLinkTag extends dmHtmlTag
{
  protected
  $resource,
  $requestContext;

  public static function build($source = null)
  {
    return self::$serviceContainer->getLinkTag($source);
  }
  
  protected function initialize()
  {
    parent::initialize();
    
    $this->addAttributeToRemove('text');
    $this->addEmptyAttributeToRemove('target', 'title');
    
    $this->addClass('link');
  }
  
  /*
   * @return string baseHref the href without query string
   */
  abstract protected function getBaseHref();
  
  public function getHrefPrefix()
  {
    return sfConfig::get('sf_no_script_name') ? $this->requestContext['prefix'] : $this->requestContext['script_name'];
  }
  
  /*
   * Set text
   * @return dmLinkTag $this
   */
  public function text($v)
  {
    return $this->set('text', (string) $v);
  }

  /*
   * Set title
   * @return dmLinkTag $this
   */
  public function title($v)
  {
    return $this->set('title', (string) $v);
  }

  /*
   * Set text and title
   * @return dmLinkTag $this
   */
  public function textTitle($v)
  {
    return $this->text($v)->title($v);
  }

  /*
   * Transform into rss link
   * @return dmLinkTag $this
   */
  public function rss($v)
  {
    return $this->set('rss', (bool) $v);
  }

  /*
   * Set link target
   * @return dmLinkTag $this
   */
  public function target($v)
  {
    if (in_array($v, array('blank', 'parent', 'self', 'top')))
    {
      $v = '_'.$v;
    }

    return $this->set('target', $v);
  }

  /*
   * Add an anchor
   * @return dmLinkTag $this
   */
  public function anchor($v)
  {
    return $this->set('anchor', trim((string) $v, '#'));
  }

  /*
   * Add a request parameter
   * @return dmLinkTag $this
   */
  public function param($key, $value)
  {
    return $this->params(array($key => $value));
  }

  /*
   * Add request parameters
   * @return dmLinkTag $this
   */
  public function params(array $params)
  {
    foreach($params as $key => $value)
    {
      $params[$key] = $value;
    }

    return $this->set('params', array_merge($this->get('params', array()), $params));
  }

  public function render()
  {
    return '<a'.$this->getHtmlAttributes().'>'.$this->renderText().'</a>';
  }

  protected function prepareAttributesForHtml(array $attributes)
  {
    $attributes = parent::prepareAttributesForHtml($attributes);

    $attributes['href'] = $this->getBaseHref();

    if (isset($attributes['params']))
    {
      if (!empty($attributes['params']))
      {
        $attributes['href'] = $this->buildUrl(
        dmString::getBaseFromUrl($attributes['href']),
        array_merge(dmString::getDataFromUrl($attributes['href']), $attributes['params'])
        );
      }
      
      unset($attributes['params']);
    }
    
    if (isset($attributes['anchor']))
    {
      $attributes['href'] .= '#'.$attributes['anchor'];
      
      unset($attributes['anchor']);
    }

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

  public function getAbsoluteHref()
  {
    $href = $this->getHref();
    
    $uriPrefix = dm::getRequest()->getUriPrefix();
     
    if (strpos($href, $uriPrefix) !== 0)
    {
      $href = $uriPrefix.$href;
    }
     
    return $href;
  }

  protected function renderText()
  {
    return $this->options['text'];
  }

  protected function buildUrl($base, array $data = array())
  {
    return $base.'?'.http_build_query($data);
  }
  
}