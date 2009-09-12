<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfObjectRouteCollection represents a collection of routes bound to objects.
 *
 * @package    symfony
 * @subpackage routing
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfObjectRouteCollection.class.php 17398 2009-04-17 16:01:14Z Kris.Wallsmith $
 */
class dmDoctrineRouteCollection extends sfDoctrineRouteCollection
{
  protected
    $routeClass = 'dmDoctrineRoute';
    
  protected function getDefaultActions()
  {
    $actions = parent::getDefaultActions();

    $actions[] = 'do';

    return $actions;
  }

  protected function getRouteForDo()
  {
    return new sfRoute(
      $this->options['prefix_path'].'/+/:action/*',
      array('module' => $this->options['module'])
    );
  }

}