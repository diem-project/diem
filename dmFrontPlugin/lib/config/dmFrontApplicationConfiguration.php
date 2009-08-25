<?php

require_once(realpath(dirname(__FILE__).'/../../../dmCorePlugin/lib/config/dmApplicationConfiguration.php'));

/**
 * sfConfiguration represents a configuration for a symfony application.
 *
 * @package    symfony
 * @subpackage config
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfApplicationConfiguration.class.php 13947 2008-12-11 14:15:32Z fabien $
 */
abstract class dmFrontApplicationConfiguration extends dmApplicationConfiguration
{
  protected function getDmPlugins()
  {
    return array('dmGuardPlugin', 'dmFrontPlugin');
  }
}