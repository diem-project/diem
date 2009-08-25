<?php

class dmFrontRouting
{

  /**
   * Listens to the routing.load_configuration event.
   *
   * @param sfEvent An sfEvent instance
   */
  public static function listenToRoutingLoadConfigurationEvent(sfEvent $event)
  {
  	$timer = dmDebug::timer('dmFrontRouting');

    $event->getSubject()->setRoutes(array(
      'default' => new sfRoute(
        '/+/:module/:action/*'
       ),
       'admin' => new sfRoute(
        '/admin',
        array(
          'module' => 'dmFront',
          'action' => 'toAdmin'
        )
       ),
      'dmPagePagination' => new sfRoute(
        '/:slug/page/:page',
        array(
          'module'  => 'dmFront',
          'action'  => 'page'
        ),
        array(
          'slug'    => '.*',
          'page'    => '\d+'
        )
      ),
      'dmHomePagination' => new sfRoute(
        '/page/:page',
        array(
          'module'  => 'dmFront',
          'action'  => 'page',
          'slug'    => ''
        ),
        array(
          'page'    => '\d+'
        )
      ),
      'dmPage' => new sfRoute(
        '/:slug',
        array(
          'module'  => 'dmFront',
          'action'  => 'page'
        ),
        array(
          'slug'    => '.*'
        )
      ),
      'homepage' => new sfRoute(
        '/',
        array(
          'module'  => 'dmFront',
          'action'  => 'page'
        )
      )
    ));

    $timer->addTime();
  }

}