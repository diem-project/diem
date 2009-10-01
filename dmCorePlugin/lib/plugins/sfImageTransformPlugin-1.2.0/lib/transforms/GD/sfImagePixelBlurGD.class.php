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
 * sfImagePixelBlurGD class.
 *
 * Blurs a GD image.
 *
 * Reduces the level of detail of the image. Slower than Guassian and Selective Blur
 *
 * @package sfImageTransform
 * @subpackage transforms
 * @author Stuart Lowes <stuart.lowes@gmail.com>
 * @version SVN: $Id$
 */
class sfImagePixelBlurGD extends sfImageTransformAbstract
{
  /**
   * The number of pixels used for the blur.
   * @var integer
  */
  protected $blur_pixels = 1;

  /**
   * Construct an sfImageBlur object.
   *
   * @param array integer
   */
  public function __construct($blur=1)
  {
    $this->setBlur($blur);
  }

  /**
   * Set the number of pixels to be read when calculating.
   *
   * @param integer
   * @return boolean
   */
  public function setBlur($pixels)
  {
    if (is_numeric($pixels))
    {
      $this->blur_pixels = $pixels;

      return true;
    }

    return false;
  }

  /**
   * Returns the number of pixels to be read when calculating.
   *
   * @return integer
   */
  public function getBlur()
  {
    return $this->blur_pixels;
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

    $resourcex = imagesx($resource);
    $resourcey = imagesy($resource);

    for ($x = 0; $x < $resourcex; ++$x)
    {
      for ($y = 0; $y < $resourcey; ++$y)
      {
        $newr = 0;
        $newg = 0;
        $newb = 0;

        $colours = array();
        $thiscol = imagecolorat($resource, $x, $y);

        for ($k = $x - $this->blur_pixels; $k <= $x + $this->blur_pixels; ++$k)
        {
          for ($l = $y - $this->blur_pixels; $l <= $y + $this->blur_pixels; ++$l)
          {
            if ($k < 0)
            {
              $colours[] = $thiscol;

              continue;
            }

            if ($k >= $resourcex)
            {
              $colours[] = $thiscol;

              continue;
            }

            if ($l < 0)
            {
              $colours[] = $thiscol;

              continue;
            }

            if ($l >= $resourcey)
            {
              $colours[] = $thiscol;

              continue;
            }

            $colours[] = imagecolorat($resource, $k, $l);
          }
        }

        foreach($colours as $colour)
        {
          $newr += ($colour >> 16) & 0xFF;
          $newg += ($colour >> 8) & 0xFF;
          $newb += $colour & 0xFF;
        }

        $numelements = count($colours);
        $newr /= $numelements;
        $newg /= $numelements;
        $newb /= $numelements;

        $newcol = imagecolorallocate($resource, $newr, $newg, $newb);
        imagesetpixel($resource, $x, $y, $newcol);
      }
    }

    return $image;
  }
}
