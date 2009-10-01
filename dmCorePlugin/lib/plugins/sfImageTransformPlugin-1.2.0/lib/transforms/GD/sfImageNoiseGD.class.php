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
 * sfImageNoiseGD class.
 *
 * Adds noise to the GD image.
 *
 * Reduces the level of detail of an image.
 *
 * @package sfImageTransform
 * @subpackage transforms
 * @author Stuart Lowes <stuart.lowes@gmail.com>
 * @version SVN: $Id$
 */
class sfImageNoiseGD extends sfImageTransformAbstract
{
  /**
   * Noise density.
  */
  protected $density = 20;

  /**
   * Construct an sfImageDuotone object.
   *
   * @param integer
   */
  public function __construct($density=20)
  {
    $this->setDensity($density);
  }

  /**
   * Sets the density
   *
   * @param integer
   * @return boolean
   */
  public function setDensity($density)
  {
    if (is_numeric($density))
    {
      $this->density = (int)$density;

      return true;
    }

    return false;
  }

  /**
   * Gets the density
   *
   * @return integer
   */
  public function getdensity()
  {
    return $this->density;
  }

  /**
   * Apply the transform to the sfImage object.
   *
   * @param sfImage
   * @return sfImage
   */
  protected function transform(sfImage $image)
  {
    $resource = $image->getAdapter()->getHolder();

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
        $modifier = rand(-$this->density,$this->density);
        $red += $modifier;
        $green += $modifier;
        $blue += $modifier;

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

    return $image;
  }
}
