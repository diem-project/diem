<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWebDebugPanelTimer adds a panel to the web debug toolbar with timer information.
 *
 * @package    symfony
 * @subpackage debug
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfWebDebugPanelTimer.class.php 12982 2008-11-13 17:25:10Z hartym $
 */
class dmWebDebugPanelTimer extends sfWebDebugPanelTimer
{
  protected function getTotalTime()
  {
    $return = sprintf('%.0f', (microtime(true) - dm::getStartTime()) * 1000);

    if(null !== self::$startTime)
  	{
  		$return .= ' -'.($return - sprintf('%.0f', (microtime(true) - self::$startTime) * 1000));
  	}

    return $return;
  }
}
