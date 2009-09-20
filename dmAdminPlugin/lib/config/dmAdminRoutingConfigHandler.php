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
    return array_merge(
      self::getDmConfiguration(),
      self::parseYamls($configFiles)
    );
  }

  public static function getDmConfiguration()
  {
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
    if ($dmMediaLibraryModule = dmModuleManager::getModuleOrNull('dmMediaLibrary'))
    {
      $config['dm_media_library_path'] = array(
        'class' => 'sfRoute',
        'url'   => $dmMediaLibraryModule->getCompleteSlug().'/path/:path',
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
    foreach(dmModuleManager::getModules() as $module)
    {
      if ($module->isProject())
      {
        if(!file_exists(dmOs::join(sfConfig::get('sf_app_dir'), 'modules', $module->getKey(), 'actions/actions.class.php')))
        {
          continue;
        }
      }
      
      if ($module->hasModel())
      {
        $config[$module->getUnderscore()] = array(
          'class' => 'dmDoctrineRouteCollection',
          'options' => array(
            'model'                 => $module->getModel(),
            'column'                => $module->getTable()->getPrimaryKey(),
            'module'                => $module->getKey(),
            'prefix_path'           => $module->getCompleteSlug(),
            'with_wildcard_routes'  => false,
            'with_show'             => false
          )
        );
      }
      else
      {
        $config[$module->getUnderscore()] = array(
          'class' => 'sfRoute',
          'url'   => $module->getCompleteSlug().'/:action/*',
          'params' => array(
            'module' => $module->getKey(),
            'action' => 'index'
          )
        );
      }
    }
    
    // static routes
    
    $config['default'] = array(
      'class' => 'sfRoute',
      'url'   => '/+/:module/:action/*'
    );
    
//    $config['dm_module_type'] = array(
//      'class' => 'sfRoute',
//      'url'   => '/:moduleTypeName',
//      'params' => array(
//        'module' => 'dmAdmin',
//        'action' => 'moduleType'
//      )
//    );
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