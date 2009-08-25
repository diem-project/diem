<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfPropelRoute represents a route that is bound to a Propel class.
 *
 * A Propel route can represent a single Propel object or a list of objects.
 *
 * @package    symfony
 * @subpackage routing
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfPropelRoute.class.php 13027 2008-11-16 16:51:25Z fabien $
 */
class dmDoctrineRoute extends sfDoctrineRoute
{

  /**
   * Constructor.
   *
   * @param string $pattern       The pattern to match
   * @param array  $defaults      An array of default parameter values
   * @param array  $requirements  An array of requirements for parameters (regexes)
   * @param array  $options       An array of options
   *
   * @see sfObjectRoute
   */
  public function __construct($pattern, array $defaults = array(), array $requirements = array(), array $options = array())
  {
  	/*
  	 * Remove .html from urls
  	 */
  	$pattern = preg_replace('|^(.+)\.:sf_format$|', '$1', $pattern);
  	
    parent::__construct($pattern, $defaults, $requirements, $options);
  }
}