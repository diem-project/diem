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
 * sfTemplateRendererInterface is the interface all renderer classes must implement.
 *
 * @package    symfony
 * @subpackage templating
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id$
 */
interface sfTemplateRendererInterface
{
  /**
   * Evaluates a template.
   *
   * @param mixed $template   The template to render
   * @param array $parameters An array of parameters to pass to the template
   *
   * @return string|false The evaluated template, or false if the renderer is unable to render the template
   */
  function evaluate($template, array $parameters = array());

  /**
   * Sets the template engine associated with this renderer.
   *
   * @param sfTemplateEngine $engine A sfTemplateEngine instance
   */
  function setEngine(sfTemplateEngine $engine);
}
