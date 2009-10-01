<?php
/*
 * This file is part of the sfImageTransform package.
 * (c) 2007 Stuart Lowes <stuart.lowes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 *
 * sfImageColorizeImageMagick class.
 *
 * Colorizes a ImageMagick image.
 *
 * @package sfImageTransform
 * @subpackage transforms
 * @author Stuart Lowes <stuart.lowes@gmail.com>
 * @version SVN: $Id$
 */
class sfImageColorizeImageMagick  extends sfImageTransformAbstract
{
  /**
   * Red Tint.
  */
  protected $red_tint = 0;

  /**
   * Green Tint.
  */
  protected $green_tint = 0;

  /**
   * Blue Tint.
  */
  protected $blue_tint = 0;

  /**
   * Alpha.
  */
  protected $alpha = 0;

  /**
   * Construct an sfImageColorize object.
   *
   * @param integer
   * @param integer
   * @param integer
   * @param integer
   */
  public function __construct($red, $green, $blue, $alpha=0)
  {
    $this->setRed($red);
    $this->setGreen($green);
    $this->setBlue($blue);
    $this->setAlpha($alpha);
  }

  /**
   * Sets the red
   *
   * @param integer
   * @return boolean
   */
  public function setRed($red)
  {
    if (is_numeric($red))
    {
      $this->red_tint = (int)$red;

      return true;
    }

    return false;
  }

  /**
   * Gets the red
   *
   * @return integer
   */
  public function getRed()
  {
    return $this->red_tint;
  }

  /**
   * Sets the green
   *
   * @param integer
   * @return boolean
   */
  public function setGreen($green)
  {
    if (is_numeric($green))
    {
      $this->green_tint = (int)$green;

      return true;
    }

    return false;
  }

  /**
   * Gets the green
   *
   * @return integer
   */
  public function getGreen()
  {
    return $this->green_tint;
  }

  /**
   * Sets the blue
   *
   * @param integer
   * @return boolean
   */
  public function setBlue($blue)
  {
    if (is_numeric($blue))
    {
      $this->blue_tint = (int)$blue;

      return true;
    }

    return false;
  }

  /**
   * Gets the blue
   *
   * @return integer
   */
  public function getBlue()
  {
    return $this->blue_tint;
  }

  /**
   * Sets the alpha
   *
   * @param integer
   * @return boolean
   */
  public function setAlpha($alpha)
  {
    if (is_numeric($alpha))
    {
      $this->alpha = (int)$alpha;

      return true;
    }

    return false;
  }

  /**
   * Gets the alpha
   *
   * @return integer
   */
  public function getAlpha()
  {
    return $this->alpha;
  }

  /**
   * Apply the transform to the sfImage object.
   *
   * @access protected
   * @param sfImage
   * @return sfImage
   */
  protected function transform(sfImage $image)
  {
    $resource = $image->getAdapter()->getHolder();

    $color = sprintf('rgb(%d,%d,%d)', $this->getRed(), $this->getGreen(), $this->getBlue());

    $pixel = new ImagickPixel($color);

    $resource->colorizeImage($pixel, $pixel);

    return $image;
  }
}
