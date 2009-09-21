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
 * sfTemplateHelperInterface is the interface all helpers must implement.
 *
 * @package    symfony
 * @subpackage templating
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id$
 */
interface sfTemplateHelperInterface
{
  /**
   * Returns the canonical name of this helper.
   *
   * @return string The canonical name
   */
  function getName();

  /**
   * Sets the helper set associated with this helper.
   *
   * @param sfTemplateHelperSet $helperSet A sfTemplateHelperSet instance
   */
  function setHelperSet(sfTemplateHelperSet $helperSet = null);

  /**
   * Gets the helper set associated with this helper.
   *
   * @return sfTemplateHelperSet A sfTemplateHelperSet instance
   */
  function getHelperSet();
}
