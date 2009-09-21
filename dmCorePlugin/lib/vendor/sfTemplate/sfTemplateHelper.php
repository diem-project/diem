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
 * sfTemplateHelper is the base class for all helper classes.
 *
 * @package    symfony
 * @subpackage templating
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id$
 */
abstract class sfTemplateHelper implements sfTemplateHelperInterface
{
  protected
    $helperSet = null;

  /**
   * Sets the helper set associated with this helper.
   *
   * @param sfTemplateHelperSet $helperSet A sfTemplateHelperSet instance
   */
  public function setHelperSet(sfTemplateHelperSet $helperSet = null)
  {
    $this->helperSet = $helperSet;
  }

  /**
   * Gets the helper set associated with this helper.
   *
   * @return sfTemplateHelperSet A sfTemplateHelperSet instance
   */
  public function getHelperSet()
  {
    return $this->helperSet;
  }
}
