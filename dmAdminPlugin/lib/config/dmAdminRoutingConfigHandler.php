<?php

class dmAdminRoutingConfigHandler extends sfRoutingConfigHandler
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
    // merge two arrays but put custom routes at the beginning
    // so that they are matched first
    $systemRoutes = self::getDmConfiguration();
    $userRoutes = parent::getConfiguration($configFiles);
    foreach ($userRoutes as $key => $value) {
      if (array_key_exists($key, $systemRoutes)) {
        $systemRoutes[$key] = $value;
      } else {
        $systemRoutes = array_reverse($systemRoutes, true);
        $systemRoutes[$key] = $value;
        $systemRoutes = array_reverse($systemRoutes, true);
      }
    }
    return $systemRoutes;
  }

  public static function getDmConfiguration()
  {
    $moduleManager = dmContext::getInstance('admin')->getModuleManager();
    
    // homepage first
    $config = array(
      'homepage' => array(
        'class' => 'sfRoute',
        'url'   => '/',
        'params' => array(
          'module' => 'dmAdmin',
          'action' => 'index'
        )
      )
    );

    // media library special route
    if ($dmMediaLibraryModule = $moduleManager->getModuleOrNull('dmMediaLibrary'))
    {
      $baseUrl = implode('/', array(
        dmString::slugify($dmMediaLibraryModule->getSpace()->getType()->getPublicName()),
        dmString::slugify($dmMediaLibraryModule->getSpace()->getPublicName()),
        dmString::slugify($dmMediaLibraryModule->getPlural())
      ));
      
      $config['dm_media_library_path'] = array(
        'class' => 'sfRoute',
        'url'   => $baseUrl.'/path/:path',
        'params' => array(
          'module' => 'dmMediaLibrary',
          'action' => 'path',
          'path'   => ''
        ),
        'requirements' => array(
          'path' => '.*'
        )
      );
    }

    // module routes
    foreach($moduleManager->getModules() as $module)
    {
      if (!$module->hasAdmin())
      {
        continue;
      }
      
      $baseUrl = implode('/', array(
        dmString::slugify($module->getSpace()->getType()->getPublicName()),
        dmString::slugify($module->getSpace()->getPublicName()),
        dmString::slugify($module->getPlural())
      ));
      
      $config[$module->getUnderscore()] = array(
        'class' => 'sfRoute',
        'url'   => $baseUrl.'/:action/*',
        'params' => array(
          'module' => $module->getSfName(),
          'action' => 'index'
        )
      );
    }
    
    // static routes
    
    $config['default'] = array(
      'class' => 'sfRoute',
      'url'   => '/+/:module/:action/*'
    );

    $config['signin'] = array(
      'class' => 'sfRoute',
      'url'   => '/security/signin',
      'params' => array(
        'module' => 'dmUserAdmin',
        'action' => 'signin'
      )
    );

    $config['signout'] = array(
      'class' => 'sfRoute',
      'url'   => '/security/signout',
      'params' => array(
        'module' => 'dmUserAdmin',
        'action' => 'signout'
      )
    );
    
    $config['dm_module_type'] = array(
      'class' => 'sfRoute',
      'url'   => '/:moduleTypeName',
      'params' => array(
        'module' => 'dmAdmin',
        'action' => 'moduleType'
      )
    );
    
    $config['dm_module_space'] = array(
      'class' => 'sfRoute',
      'url'   => '/:moduleTypeName/:moduleSpaceName',
      'params' => array(
        'module' => 'dmAdmin',
        'action' => 'moduleSpace'
      )
    );
    
    return $config;
  }
}
