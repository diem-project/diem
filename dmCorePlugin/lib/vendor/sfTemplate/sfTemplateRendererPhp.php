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
 * sfTemplateRendererPhp is a renderer for PHP templates.
 *
 * @package    symfony
 * @subpackage templating
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id$
 */
class sfTemplateRendererPhp extends sfTemplateRenderer
{
  /**
   * Evaluates a template.
   *
   * @param mixed $template   The template to render
   * @param array $parameters An array of parameters to pass to the template
   *
   * @return string|false The evaluated template, or false if the renderer is unable to render the template
   */
  public function evaluate($template, array $parameters = array())
  {
    if ($template instanceof sfTemplateStorageFile)
    {
      extract($parameters);
      ob_start();
      require $template;

      return ob_get_clean();
    }
    else if (is_string($template) || $template instanceof sfTemplateStorageString)
    {
      extract($parameters);
      ob_start();
      eval('; ?>'.$template.'<?php ;');

      return ob_get_clean();
    }

    return false;
  }
}
