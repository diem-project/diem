<?php

class dmAdminRouting
{

  /**
   * Listens to the routing.load_configuration event.
   *
   * @param sfEvent An sfEvent instance
   */
  static public function listenToRoutingLoadConfigurationEvent(sfEvent $event)
  {
  	$timer = dmDebug::timer('dmAdminRouting');

    $r = $event->getSubject();

    $routes = array();

    // preprend our routes
    $routes['homepage'] = new sfRoute('/', array('module' => 'dmAdmin', 'action' => 'index'));

    $routes['dm_module_type'] = new sfRoute('/:moduleTypeName', array('module' => 'dmAdmin', 'action' => 'moduleType'));
    $routes['dm_module_space'] = new sfRoute('/:moduleTypeName/:moduleSpaceName', array('module' => 'dmAdmin', 'action' => 'moduleSpace'));

    if ($dmMediaLibraryModule = dmModuleManager::getModuleOrNull('dmMediaLibrary'))
    {
	    $routes['dm_media_library_path'] = new sfRoute(
	      $dmMediaLibraryModule->getCompleteSlug().'/path/:path',
	      array('module' => 'dmMediaLibrary', 'action' => 'path', 'path' => ''),
	      array('path' => '.*')
	    );
    }

    $routes = self::loadModulesRoute($routes);

    $routes['default'] = new sfRoute('/+/:module/:action/*', array());
    $routes['default_index'] = new sfRoute('/+/:module/*', array('action' => 'index'));

    $timer3 = dmDebug::timer('dmAdminRouting::setRoutes');
    $event->getSubject()->setRoutes($routes);
    $timer3->addTime();

    $timer->addTime();
  }

  protected static function loadModulesRoute($routes)
  {
    foreach(dmModuleManager::getModules() as $module)
    {
      if ($module->hasModel())
      {
        $timer2 = dmDebug::timer('dmAdminRouting::doctrineRoute');
        $route = new dmDoctrineRouteCollection(array(
          'name'                 => $module->getUnderscore(),
          'model'                => $module->getModel(),
          'column'               => $module->getTable()->getPrimaryKey(),
          'module'               => $module->getKey(),
          'prefix_path'          => $module->getCompleteSlug(),
          'with_wildcard_routes' => true,
          'with_show'            => false,
          'requirements'         => array()
        ));
        $timer2->addTime();
      }
      else
      {
        $route = new sfRoute(
          $module->getCompleteSlug().'/:action/*',
          array('module' => $module->getKey(), 'action' => 'index')
        );
      }

      $routes[$module->getUnderscore()] = $route;
    }

    return $routes;
  }

}