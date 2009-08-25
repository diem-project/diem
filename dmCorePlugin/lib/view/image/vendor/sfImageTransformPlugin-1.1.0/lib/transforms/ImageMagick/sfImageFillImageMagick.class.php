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
 * sfImageFillImageMagick class.
 *
 * Fills the set area with a color or tile image.
 *
 * @package sfImageTransform
 * @subpackage transforms
 * @author Stuart Lowes <robin@ngse.co.uk>
 * @version SVN: $Id$
 */
class sfImageFillImageMagick extends sfImageTransformAbstract
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
   * Fuzz
   *
   * @var integer
   */
  protected $fuzz = 0;

  /**
   * Border
   *
   * @var String
   */
  protected $border = null;

  /**
   * Construct an sfImageDuotone object.
   *
   * @param integer
   * @param integer
   * @param String/object hex color
   * @param integer
   * @param String/object hex color
   */
  public function __construct($x=0, $y=0, $fill='#000000', $fuzz=0, $border=null)
  {
    $this->setX($x);
    $this->setY($y);
    $this->setFill($fill);
    $this->setFuzz($fuzz);
    $this->setBorder($border);
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
   * Sets the fuzz
   *
   * @param integer $fuzz
   * @return boolean
   */
  public function setFuzz($fuzz)
  {
    if (is_numeric($fuzz))
    {
      $this->fuzz = (int)$fuzz;

      return true;
    }

    return false;
  }

  /**
   * Gets the fuzz
   *
   * @return integer
   */
  public function getFuzz()
  {
    return $this->fuzz;
  }

  /**
   * Sets the border colour.
   *
   * @param String $border
   * @return boolean
   */
  public function setBorder($border)
  {
    if (preg_match('/#[\d\w]{6}/',$border))
    {
      $this->border = $border;

      return true;
    }

    return false;
  }

  /**
   * Gets the border colour.
   *
   * @return String
   */
  public function getBorder()
  {
    return $this->border;
  }

  /**
   * Sets the fill
   *
   * @param mixed
   * @return boolean
   */
  public function setFill($fill)
  {
    if (preg_match('/#[\d\w]{6}/',$fill))
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

    $fill = new ImagickPixel();
    $fill->setColor($this->fill);
    echo 

    $border = new ImagickPixel();
    $border->setColor($this->border);

    $resource->colorFloodfillImage($fill, $this->fuzz, $border, $this->x, $this->y);

    return $image;
  }
}
