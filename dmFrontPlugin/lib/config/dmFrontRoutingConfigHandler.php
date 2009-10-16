<?php

class dmFrontRoutingConfigHandler extends sfRoutingConfigHandler
{
  
  protected function parse($configFiles)
  {
    // parse the yaml
    $rawConfig = self::getConfiguration($configFiles);
    
    $dmFrontConfig = self::parseYamls(array(
      dmOs::join(sfConfig::get('dm_front_dir'), 'config/routing.yml')
    ));
    
    $firstConfig = array();
    $lastConfig = array();
    foreach($rawConfig as $key => $value)
    {
      if (array_key_exists($key, $dmFrontConfig))
      {
        $lastConfig[$key] = $value;
      }
      else
      {
        $firstConfig[$key] = $value;
      }
    }
    $config = array_merge($firstConfig, $lastConfig);

    // collect routes
    $routes = array();
    foreach ($config as $name => $params)
    {
      if (
        (isset($params['type']) && 'collection' == $params['type'])
        ||
        (isset($params['class']) && false !== strpos($params['class'], 'Collection'))
      )
      {
        $options = isset($params['options']) ? $params['options'] : array();
        $options['name'] = $name;
        $options['requirements'] = isset($params['requirements']) ? $params['requirements'] : array();

        $routes[$name] = array(isset($params['class']) ? $params['class'] : 'sfRouteCollection', array($options));
      }
      else
      {
        $routes[$name] = array(isset($params['class']) ? $params['class'] : 'sfRoute', array(
          $params['url'] ? $params['url'] : '/',
          isset($params['params']) ? $params['params'] : (isset($params['param']) ? $params['param'] : array()),
          isset($params['requirements']) ? $params['requirements'] : array(),
          isset($params['options']) ? $params['options'] : array(),
        ));
      }
    }

    return $routes;
  }
  
}
