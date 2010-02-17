<?php

class dmFrontLinkTagRoute extends dmFrontLinkTag
{
  protected
  $route,
  $controller,
  $routing;
  
  public function __construct(dmFrontLinkResource $resource, sfWebController $controller, dmFrontRouting $routing, array $requestContext, array $options = array())
  {
    $this->controller = $controller;
    $this->routing    = $routing;
    
    parent::__construct($resource, $requestContext, $options);
  }
  
  protected function initialize(array $options = array())
  {
    parent::initialize($options);
    
    $this->route = $this->resource->getSubject();
  }

  protected function getBaseHref($return_array = false)
  {
    list($route_name, $route_parameters) = $this->controller->convertUrlStringToParameters($this->route);
    $routes = $this->routing->getRoutes();
    
    if (!isset($routes[$route_name]))
    {           
      throw new sfConfigurationException(sprintf('The route "%s" does not exist.', $route_name));
    }    
                
    /** @var sfRoute */
    $sf_route = $routes[$route_name];
    $route_variables = $sf_route->getVariables();
    $params = $this->get('params', array());

    foreach ($route_variables as $key => $value)
    {
      if (isset($params[$key]))
      {
        $route_parameters[$key] = $params[$key];
        unset ($params[$key]);
      }
    }

    $href = $this->controller->genUrl(array_merge(
      array(
         'sf_route' => $route_name
      ), $route_parameters
    ));
    
    
    
    return $return_array ? array ($href, $params) : $href;
  }
       
   protected function prepareAttributesForHtml(array $attributes)
  {
    $attributes = parent::prepareAttributesForHtml($attributes);

    list($attributes['href'], $attributes['params']) = $this->getBaseHref(true);

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

    // makes unit testing easier
    ksort($attributes);
    
    return $attributes;
  }
  
}