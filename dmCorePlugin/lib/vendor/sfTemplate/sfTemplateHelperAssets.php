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
 * sfTemplateHelperAssets is the base class for all helper classes that manages assets.
 *
 * Usage:
 *
 * <code>
 *   <img src="<?php echo $this->assets->getUrl('foo.png') ?>" />
 * </code>
 *
 * @package    symfony
 * @subpackage templating
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id$
 */
class sfTemplateHelperAssets extends sfTemplateHelper
{
  protected
    $version  = null,
    $baseURLs = array(),
    $basePath = '';

  /**
   * Constructor.
   *
   * @param string       $basePath The base path
   * @param string|array $baseURLs The domain URL or an array of domain URLs
   * @param string       $version  The version
   */
  public function __construct($basePath = null, $baseURLs = array(), $version = null)
  {
    $this->setBasePath($basePath);
    $this->setBaseURLs($baseURLs);
    $this->version = $version;
  }

  /**
   * Gets the version to add to public URL.
   *
   * @return string The current version
   */
  public function getVersion()
  {
    return $this->version;
  }

  /**
   * Sets the version that is added to each public URL.
   *
   * @param string $id The version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }

  /**
   * Gets the base path.
   *
   * @return string The base path
   */
  public function getBasePath()
  {
    return $this->basePath;
  }

  /**
   * Sets the base path.
   *
   * @param string $basePath The base path
   */
  public function setBasePath($basePath)
  {
    if ($basePath && '/' != $basePath[0])
    {
      $basePath = '/'.$basePath;
    }

    $this->basePath = rtrim($basePath, '/').'/';
  }

  /**
   * Gets the base URL.
   *
   * @return string The base URL
   */
  public function getBaseURL($path)
  {
    if (!$this->baseURLs)
    {
      return '';
    }
    elseif (1 == count($this->baseURLs))
    {
      return $this->baseURLs[0];
    }
    else
    {
      return $this->baseURLs[fmod(hexdec(substr(md5($path), 0, 10)), count($this->baseURLs))];
    }
  }

  /**
   * Gets the base URLs.
   *
   * @return array The base URLs
   */
  public function getBaseURLs()
  {
    return $this->baseURLs;
  }

  /**
   * Sets the base URLs.
   *
   * If you pass an array, the getBaseURL() will return a
   * random one each time it is called.
   *
   * @param string|array $baseURLs The base URLs
   */
  public function setBaseURLs($baseURLs)
  {
    if (!is_array($baseURLs))
    {
      $baseURLs = array($baseURLs);
    }

    $this->baseURLs = array();
    foreach ($baseURLs as $URL)
    {
      $this->baseURLs[] = rtrim($URL, '/');
    }
  }

  /**
   * Returns the public path.
   *
   * @param string $path A public path
   *
   * @return string A public path which takes into account the base path and URL path
   */
  public function getUrl($path)
  {
    if (strpos($path, '://'))
    {
      return $path;
    }

    if (0 !== strpos($path, '/'))
    {
      $path = $this->basePath.$path;
    }

    return $this->getBaseURL($path).$path.($this->version ? '?'.$this->version : '');
  }

  /**
   * Returns the canonical name of this helper.
   *
   * @return string The canonical name
   */
  public function getName()
  {
    return 'assets';
  }
}
