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
 * sfImageResizeImageMagick class.
 *
 * Rotates a ImageMagick image.
 *
 * Rotates image by a set angle.
 *
 * @package sfImageTransform
 * @subpackage transforms
 * @author Stuart Lowes <stuart.lowes@gmail.com>
 * @version SVN: $Id$
 */
class sfImageRotateImageMagick extends sfImageTransformAbstract
{
  /**
   * Angle to rotate
   *
   * @param integer
   */
  protected $angle;

  /**
   * Background color.
   *
   * @param integer
   */
  protected $background = '';

  /**
   * Construct an sfImageCrop object.
   *
   * @param integer
   * @param string
   */
  public function __construct($angle, $background='')
  {
    $this->setAngle($angle);
    $this->setBackgroundColor($background);
  }

  /**
   * set the angle to rotate the image by.
   *
   * @param integer
   */
  public function setAngle($angle)
  {
    $this->angle = $angle;
  }

  /**
   * Gets the angle to rotate the image by.
   *
   * @return integer
   */
  public function getAngle()
  {
    return $this->angle;
  }

  /**
   * set the background color for the image.
   *
   * @param integer
   */
  public function setBackgroundColor($color)
  {
    $this->background = $color;
  }

  /**
   * Gets the background color
   *
   * @return integer
   */
  public function getBackgroundColor()
  {
    return $this->background;
  }

  /**
   * Apply the transform to the sfImage object.
   *
   * @param sfImage
   * @return sfImage
   */
  protected function transform(sfImage $image)
  {
    // No need to do anything
    if ($this->angle == 0)
    {
      return $image;
    }

    $resource = $image->getAdapter()->getHolder();

    // By default use the background of the top left corner
    if ($this->background === '')
    {
      $this->background = $resource->getImagePixelColor(0, 0);
    }

    $resource->rotateImage($this->background, $this->angle);

    return $image;
  }
}
