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
 * sfImageResizeSimpleImageMagick class.
 *
 * Resizes image.
 *
 * Resizes the image to the set size.
 *
 * @package sfImageTransform
 * @subpackage transforms
 * @author Stuart Lowes <stuart.lowes@gmail.com>
 * @version SVN: $Id$
 */
class sfImageResizeSimpleImageMagick extends sfImageTransformAbstract
{
  /**
   * Image width.
   * var integer width of the image is to be reized to
  */
  protected $width = 0;

  /**
   * Image height.
   * var integer height of the image is to be reized to
  */
  protected $height = 0;

  /**
   * Construct an sfImageCrop object.
   *
   * @param integer
   * @param integer
   */
  public function __construct($width, $height)
  {
    $this->setWidth($width);
    $this->setHeight($height);
  }

  /**
   * Set the images new width.
   *
   * @param integer
   */
  public function setWidth($width)
  {
    $this->width = (int)$width;
  }

  /**
   * Gets the images new width
   *
   * @return integer
   */
  public function getWidth()
  {
    return $this->width;
  }

  /**
   * Set the images new height.
   *
   * @param integer
   */
  public function setHeight($height)
  {
    $this->height = (int)$height;
  }

  /**
   * Gets the images new height
   *
   * @return integer
   */
  public function getHeight()
  {
    return $this->height;
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

    $x = $resource->getImageWidth();
    $y = $resource->getImageHeight();

    // If the width or height is not valid then enforce the aspect ratio
    if (!is_numeric($this->width) || $this->width < 1)
    {
      $this->width = round(($x / $y) * $this->height);
    }

    else if (!is_numeric($this->height) || $this->height < 1)
    {
      $this->height = round(($y / $x) * $this->width);
    }

    $resource->resizeImage($this->width, $this->height, Imagick::FILTER_LANCZOS, 1);

    return $image;
  }
}
