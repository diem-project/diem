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
 * sfImageTrimImageMagick class.
 *
 * Trims a ImageMagick image.
 *
 * Trims an image using a specific colour or the colour of the top left of the image.
 *
 * @package sfImageTransform
 * @subpackage transforms
 * @author Robin Corps <robin@ngse.co.uk>
 * @version SVN: $Id$
 */
class sfImageTrimImageMagick extends sfImageTransformAbstract
{
  /**
   * tolerence for the trim.
   *
   * @var float
  */
  protected $fuzz = 0;

  /**
   * Background color.
   *
   * @var integer
  */
  protected $background = null;

  /**
   * Construct an sfImageCrop object.
   *
   * @param integer
   * @param string
   */
  public function __construct($fuzz=0, $background=null)
  {
    $this->setFuzz($fuzz);
    $this->setBackgroundColor($background);
  }

  /**
   * set the angle to rotate the image by.
   *
   * @param integer
   */
  public function setFuzz($fuzz)
  {
    if (!is_numeric($fuzz))
    {
      $this->fuzz = (float)$fuzz;

      return true;
    }

    return false;
  }

  /**
   * Gets the angle to rotate the image by.
   *
   * @return integer
   */
  public function getFuzz()
  {
    return $this->fuzz;
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
   * Gets the angle to rotate the image by.
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
    $resource = $image->getAdapter()->getHolder();

    // By default use the background of the top left corner
    if (is_null($this->background))
    {
      $this->background = $resource->getImagePixelColor(0, 0);
      $background = $this->background;
    }
    else
    {
      $background = new ImagickPixel();
      $background->setColor($this->background);
    }

    $resource->setBackgroundColor($background);
    $resource->trimImage($this->fuzz);

    return $image;
  }
}
