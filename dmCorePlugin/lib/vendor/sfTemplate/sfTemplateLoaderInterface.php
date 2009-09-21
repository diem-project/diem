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
 * sfTemplateLoaderInterface is the interface all loaders must implement.
 *
 * @package    symfony
 * @subpackage templating
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id$
 */
interface sfTemplateLoaderInterface
{
  /**
   * Loads a template.
   *
   * @param string $template The logical template name
   * @param string $renderer The renderer to use
   *
   * @return string|Boolean false if the template cannot be loaded, the loaded template otherwise
   */
  function load($template, $renderer = 'php');
}
