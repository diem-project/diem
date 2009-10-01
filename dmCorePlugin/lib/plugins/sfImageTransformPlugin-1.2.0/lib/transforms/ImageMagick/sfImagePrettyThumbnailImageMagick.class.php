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
 * sfImagePrettyThumbnailImageMagick class.
 *
 * Makes a "pretty" thumbnail image.
 *
 * Scales, adds drop shadow and rounded corners
 *
 * @package sfImageTransform
 * @subpackage transforms
 * @author Stuart Lowes <stuart.lowes@gmail.com>
 * @version SVN: $Id$
 */
class sfImagePrettyThumbnailImageMagick extends sfImageTransformAbstract
{
  /**
   * The thumbnail width
   *
   * @var integer
  */
  protected $width = null;

  /**
   * The thumbnail height
   *
   * @var integer
  */
  protected $height = null;

  /**
   * The thumbnail corner radius
   *
   * @var integer
  */
  protected $radius = null;

  /**
   * Construct an sfImageScale object.
   *
   * @param float
   */
  public function __construct($width, $height, $radius)
  {
    $this->setWidth($width);
    $this->setHeight($height);
    $this->setRadius($radius);
  }

  /**
   * Set the thumbnail's width.
   *
   * @param integer
   */
  public function setWidth($width)
  {
    if (is_numeric($width))
    {
      $this->width = (int)$width;
    }
  }

  /**
   * Gets the thumbnail's width.
   *
   * @return integer
   */
  public function getWidth()
  {
    return $this->width;
  }

  /**
   * Set the thumbnail's height.
   *
   * @param integer
   */
  public function setHeight($height)
  {
    if (is_numeric($height))
    {
      $this->height = (int)$height;
    }
  }

  /**
   * Gets the thumbnail's height.
   *
   * @return integer
   */
  public function getHeight()
  {
    return $this->height;
  }

  /**
   * Set the thumbnail's corner radius.
   *
   * @param integer
   */
  public function setRadius($radius)
  {
    if (is_numeric($radius))
    {
      $this->radius = (int)$radius;
    }
  }

  /**
   * Gets the thumbnail's radius.
   *
   * @return integer
   */
  public function getRadius()
  {
    return $this->radius;
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

    $image->resize( $this->getWidth(), $this->getHeight() );

    $resource->roundCorners( $this->getRadius(), $this->getRadius() );

    $shadow = $resource->clone();

    $shadow->setImageBackgroundColor( new ImagickPixel( 'black' ) );

    $shadow->shadowImage( 80, 3, 5, 5 );

    $shadow->compositeImage( $resource, Imagick::COMPOSITE_OVER, 0, 0 );

    return $image;

  }
}
