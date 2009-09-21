<?php

/*
 * This file is part of the symfony package.
 *
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfTemplateLoader is the base class for all template loader classes.
 *
 * @package    symfony
 * @subpackage templating
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id$
 */
abstract class sfTemplateLoader implements sfTemplateLoaderInterface
{
  protected
    $debugger = null;

  /**
   * Sets the debugger to use for this loader.
   *
   * @param sfTemplateDebuggerInterface $debugger A debugger instance
   */
  public function setDebugger(sfTemplateDebuggerInterface $debugger)
  {
    $this->debugger = $debugger;
  }
}
