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
 * sfTemplateLoaderFilesystem is a loader that read templates from the filesystem.
 *
 * @package    symfony
 * @subpackage templating
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id$
 */
class sfTemplateLoaderFilesystem extends sfTemplateLoader
{
  protected
    $templatePathPatternPatterns = array();

  /**
   * Constructor.
   *
   * @param array $templatePathPatterns An array of path patterns to look for templates
   */
  public function __construct($templatePathPatterns)
  {
    if (!is_array($templatePathPatterns))
    {
      $templatePathPatterns = array($templatePathPatterns);
    }

    $this->templatePathPatterns = $templatePathPatterns;
  }

  /**
   * Loads a template.
   *
   * @param string $template The logical template name
   * @param string $renderer The renderer to use
   *
   * @return string|Boolean false if the template cannot be loaded, the loaded template otherwise
   */
  public function load($template, $renderer = 'php')
  {
    if (self::isAbsolutePath($template) && file_exists($template))
    {
      return new sfTemplateStorageFile($template);
    }

    foreach ($this->templatePathPatterns as $templatePathPattern)
    {
      if (is_file($file = strtr($templatePathPattern, array('%name%' => $template, '%renderer%' => $renderer))))
      {
        if ($this->debugger)
        {
          $this->debugger->log(sprintf('Loaded template file "%s" (renderer: %s)', $file, $renderer));
        }

        return new sfTemplateStorageFile($file);
      }

      if ($this->debugger)
      {
        $this->debugger->log(sprintf('Failed loading template file "%s" (renderer: %s)', $file, $renderer));
      }
    }

    return false;
  }

  /**
   * Returns true if the file is an existing absolute path.
   *
   * @param string $file A path
   *
   * @return true if the path exists and is absolute, false otherwise
   */
  static protected function isAbsolutePath($file)
  {
    if ($file[0] == '/' || $file[0] == '\\' ||
        (strlen($file) > 3 && ctype_alpha($file[0]) &&
         $file[1] == ':' &&
         ($file[2] == '\\' || $file[2] == '/')
        )
       )
    {
      return true;
    }

    return false;
  }
}
