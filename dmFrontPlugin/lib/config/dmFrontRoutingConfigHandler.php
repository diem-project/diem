<?php

class dmFrontRoutingConfigHandler extends sfRoutingConfigHandler
{
  /*
   * No change from Symfony
   * Overloaded to make the self::configuration call point to this class
   */
  protected function parse($configFiles)
  {
    // parse the yaml
    $config = self::getConfiguration($configFiles);
    
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

  /**
   * @see sfConfigHandler
   */
  static public function getConfiguration(array $configFiles)
  {
    return array_merge(
      self::getDmConfiguration(),
      self::parseYamls($configFiles)
    );
  }

  public static function getDmConfiguration()
  {
    return array(
      'default' => array(
        'class' => 'sfRoute',
        'url'   => '/+/:module/:action/*'
      ),
      'admin' => array(
        'class' => 'sfRoute',
         'url'  => '/admin',
         'params' => array(
           'module' => 'dmFront',
           'action' => 'toAdmin'
         )
       ),
      'dmPagePagination' => array(
        'class' => 'sfRoute',
         'url'  => '/:slug/page/:page',
         'params' => array(
           'module' => 'dmFront',
           'action' => 'page'
         ),
         'requirements' => array(
           'slug'   => '.*',
           'page'   => '\d+'
         )
       ),
      'dmHomePagination' => array(
        'class' => 'sfRoute',
         'url'  => '/page/:page',
         'params' => array(
           'module' => 'dmFront',
           'action' => 'page'
         ),
         'requirements' => array(
           'page'   => '\d+'
         )
       ),
      'dmPage' => array(
        'class' => 'sfRoute',
         'url'  => '/:slug',
         'params' => array(
           'module' => 'dmFront',
           'action' => 'page'
         ),
         'requirements' => array(
           'slug'   => '.*'
         )
       ),
      'homepage' => array(
        'class' => 'sfRoute',
        'url'  => '/',
        'params' => array(
          'module' => 'dmFront',
          'action' => 'page'
        )
      )
    );
  }
}