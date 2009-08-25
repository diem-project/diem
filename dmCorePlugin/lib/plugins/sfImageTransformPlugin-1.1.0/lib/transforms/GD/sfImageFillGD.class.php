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
 * sfImageFillGD class.
 *
 * Fills the set area with a color or tile image.
 *
 * @package sfImageTransform
 * @subpackage transforms
 * @author Stuart Lowes <stuart.lowes@gmail.com>
 * @version SVN: $Id$
 */
class sfImageFillGD extends sfImageTransformAbstract
{
  /**
   * x-coordinate.
   * @var integer
  */
  protected $x = 0;

  /**
   * y-coordinate
   * @var integer
  */
  protected $y = 0;

  /**
   * Fill.
  */
  protected $fill = null;

  /**
   * Construct an sfImageDuotone object.
   *
   * @param integer
   * @param integer
   * @param string/object hex color or sfImage object
   */
  public function __construct($x=0, $y=0, $fill='#000000')
  {
    $this->setX($x);
    $this->setY($y);
    $this->setFill($fill);
  }

  /**
   * Sets the X coordinate
   *
   * @param integer
   * @return boolean
   */
  public function setX($x)
  {
    if (is_numeric($x))
    {
      $this->x = (int)$x;

      return true;
    }

    return false;
  }

  /**
   * Gets the X coordinate
   *
   * @return integer
   */
  public function getX()
  {
    return $this->x;
  }

  /**
   * Sets the Y coordinate
   *
   * @param integer
   * @return boolean
   */
  public function setY($y)
  {
    if (is_numeric($y))
    {
      $this->y = (int)$y;

      return true;
    }

    return false;
  }

  /**
   * Gets the Y coordinate
   *
   * @return integer
   */
  public function getY()
  {
    return $this->y;
  }

  /**
   * Sets the fill
   *
   * @param mixed
   * @return boolean
   */
  public function setFill($fill)
  {
    if (preg_match('/#[\d\w]{6}/',$fill) || (is_object($fill) && class_name($fill) === 'sfImage'))
    {
      $this->fill = $fill;

      return true;
    }

    return false;
  }

  /**
   * Gets the fill
   *
   * @return mixed
   */
  public function getFill()
  {
    return $this->fill;
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

    if (is_object($this->fill))
    {
      imagesettile($resource, $this->fill->getAdapter()->getHolder());
      imagefill($resource, $this->x, $this->y, IMG_COLOR_TILED);
    }

    else
    {
      imagefill($resource, $this->x, $this->y, $image->getAdapter()->getColorByHex($resource, $this->fill));
    }

    return $image;
  }
}
