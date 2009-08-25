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
 * sfImageColorizeGD class.
 *
 * Colorizes a GD image.
 *
 * @package sfImageTransform
 * @subpackage transforms
 * @author Stuart Lowes <stuart.lowes@gmail.com>
 * @version SVN: $Id$
 */
class sfImageColorizeGD  extends sfImageTransformAbstract
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


    // Use GD's built in filter
    if (function_exists('imagefilter'))
    {
      imagefilter($resource,IMG_FILTER_COLORIZE, $this->red_tint, $this->green_tint, $this->blue_tint, $this->alpha);

    }

    // Else do filter in code
    // Alpha not supported
    else
    {
      $resourcex = imagesx($resource);
      $resourcey = imagesy($resource);

      for ($x = 0; $x < $resourcex; ++$x)
      {
        for ($y = 0; $y < $resourcey; ++$y)
        {
          $rgb = imagecolorat($resource, $x, $y);
          $red = ($rgb >> 16) & 0xFF;
          $green = ($rgb >> 8) & 0xFF;
          $blue = $rgb & 0xFF;
          $red = (int)(($red+$green+$blue)/3);
          $green = $red + $this->green_tint;
          $blue = $red + $this->blue_tint;
          $red += $this->red_tint;

          // Max value is 255
          // Min value is 0

          if ($red > 255)
          {
            $red = 255;
          }

          if ($green > 255)
          {
            $green = 255;
          }

          if ($blue > 255)
          {
            $blue = 255;
          }

          if ($red < 0)
          {
            $red = 0;
          }

          if ($green < 0)
          {
            $green = 0;
          }

          if ($blue < 0)
          {
            $blue = 0;
          }

          $newcol = imagecolorallocate ($resource, $red,$green,$blue);
          imagesetpixel ($resource, $x, $y, $newcol);
        }
      }
    }

    return $image;
  }
}
