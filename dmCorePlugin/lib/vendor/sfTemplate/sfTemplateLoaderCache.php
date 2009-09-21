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
 * sfTemplateLoaderCache is a loader that caches other loaders responses
 * on the filesystem.
 *
 * This cache only caches on disk to allow PHP accelerators to cache the opcodes.
 * All other mecanism would imply the use of `eval()`.
 *
 * @package    symfony
 * @subpackage templating
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id$
 */
class sfTemplateLoaderCache extends sfTemplateLoader
{
  protected
    $loader = null,
    $dir    = '';

  /**
   * Constructor.
   *
   * @param sfTemplateLoader $loader A sfTemplateLoader instance
   * @param string           $dir    The directory where to store the cache files
   */
  public function __construct(sfTemplateLoader $loader, $dir)
  {
    $this->loader = $loader;
    $this->dir = $dir;
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
    $path = $this->dir.DIRECTORY_SEPARATOR.md5($template.$renderer).'.tpl';

    if ($this->loader instanceof sfTemplateLoaderCompilableInterface)
    {
      $renderer = 'php';
    }

    if (file_exists($path))
    {
      if ($this->debugger)
      {
        $this->debugger->log(sprintf('Fetching template "%s" from cache', $template));
      }

      return new sfTemplateStorageFile($path, $renderer);
    }

    if (false === $content = $this->loader->load($template, $renderer))
    {
      return false;
    }

    if ($this->loader instanceof sfTemplateLoaderCompilableInterface)
    {
      $content = $this->loader->compile($content);
    }

    file_put_contents($path, $content);

    if ($this->debugger)
    {
      $this->debugger->log(sprintf('Storing template "%s" in cache', $template));
    }

    return new sfTemplateStorageFile($path, $renderer);
  }
}
