<?php

/*
 * This file is part of the sfImageTransformPlugin package.
 * (c) 2007 Stuart Lowes <stuart.lowes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfImageTransformException is thrown when an fatal error occurs while manipulating a image.
 *
 * @package   sfImageTransform
 * @subpackage exceptions
 * @author   Stuart Lowes <stuart.lowes@gmail.com>
 * @version   SVN: $Id$
 */
class sfImageTransformException extends sfException
{
  /**
   * Class constructor.
   *
   * @param string error message
   * @param int error code
   */
  public function __construct($message = null, $code = 0)
  {
    // Legacy support for 1.0
  	if (method_exists($this, 'setName'))
  	{
	    $this->setName('sfImageTransformException');
  	}

  	parent::__construct($message, $code);
  }
}
