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
 * sfImageArcGD class.
 *
 * Draws an arc.
 *
 * Draws an arc on an GD image.
 *
 * @package sfImageTransform
 * @subpackage transforms
 * @author Stuart Lowes <stuart.lowes@gmail.com>
 * @version SVN: $Id$
 */
class sfImageArcGD extends sfImageTransformAbstract
{
  /**
   * X-coordinate of the center.
   * @var integer
  */
  protected $x = 0;

  /**
   * Y-coordinate of the center.
   * @var integer
  */
  protected $y = 0;

  /**
   * The arc width
   * @var integer
  */
  protected $width = 0;

  /**
   * The arc height
   * @var integer
  */
  protected $height = 0;

  /**
   * Line thickness
   * @var integer
  */
  protected $thickness = 0;

  /**
   * The arc start angle, in degrees.
   * @var integer
  */
  protected $start_angle = 0;

  /**
   * The arc end angle, in degrees.
   * @var integer
  */
  protected $end_angle = 90;

  /**
   * Line color.
   * @var string hex
  */
  protected $color = '#000000';

  /**
   * Fill.
   * @var string/sfImage hex color or sfImage
  */
  protected $fill = null;

  /**
   * Line style.
   * @var integer
  */
  protected $style = null;

  /**
   * Construct an sfImageArc object.
   *
   * @param integer $x x coordinate
   * @param integer $y y coordinate
   * @param integer $width width of arc
   * @param integer $height height of arc
   * @param integer $start_angle angle in degrees
   * @param integer $end_angle angle in degrees
   * @param integer $thickness line thickness
   * @param string  $color hex color of line
   * @param string/object $fill string color or fill object
   * @param integer $style fill style, only applicable if using a fill object
   */
  public function __construct($x, $y, $width, $height, $start_angle, $end_angle, $thickness = 1, $color = '#000000', $fill=null,$style = null )
  {
    $this->setX($x);
    $this->setY($y);
    $this->setWidth($width);
    $this->setHeight($height);
    $this->setStartAngle($start_angle);
    $this->setEndAngle($end_angle);
    $this->setThickness($thickness);
    $this->setColor($color);
    $this->setFill($fill);
    $this->setStyle($style);
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
   * Sets the width
   *
   * @param integer
   * @return boolean
   */
  public function setWidth($width)
  {
    if (is_numeric($width))
    {
      $this->width = (int)$width;

      return true;
    }

    return false;
  }

  /**
   * Gets the Width
   *
   * @return integer
   */
  public function getWidth()
  {
    return $this->width;
  }

  /**
   * Sets the height
   *
   * @param integer
   * @return boolean
   */
  public function setHeight($height)
  {
    if (is_numeric($height))
    {
      $this->height = (int)$height;

      return true;
    }

    return false;
  }

  /**
   * Gets the height
   *
   * @return integer
   */
  public function getHeight()
  {
    return $this->height;
  }

  /**
   * Sets the start angel
   *
   * @param integer
   * @return boolean
   */
  public function setStartAngle($start_angle)
  {
    if (is_numeric($start_angle))
    {
      $this->start_angle = (int)$start_angle;

      return true;
    }

    return false;
  }

  /**
   * Gets the start angel
   *
   * @return integer
   */
  public function getStartAngle()
  {
    return $this->start_angle;
  }

  /**
   * Sets the end angel
   *
   * @param integer
   * @return boolean
   */
  public function setEndAngle($end_angle)
  {
    if (is_numeric($end_angle))
    {
      $this->end_angle = (int)$end_angle;

      return true;
    }

    return false;
  }

  /**
   * Gets the end angel
   *
   * @return integer
   */
  public function getEndAngle()
  {
    return $this->end_angle;
  }

  /**
   * Sets the thickness
   *
   * @param integer
   * @return boolean
   */
  public function setThickness($thickness)
  {
    if (is_numeric($thickness))
    {
      $this->thickness = (int)$thickness;
      return true;
    }

    return false;
  }

  /**
   * Gets the thickness
   *
   * @return integer
   */
  public function getThickness()
  {
    return $this->thickness;
  }

  /**
   * Sets the color
   *
   * @param string
   * @return boolean
   */
  public function setColor($color)
  {
    if (preg_match('/#[\d\w]{6}/',$color))
    {
      $this->color = $color;
      return true;
    }
    return false;
  }

  /**
   * Gets the color
   *
   * @return integer
   */
  public function getColor()
  {
    return $this->color;
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
   * Sets the style
   *
   * @param integer
   * @return boolean
   */
  public function setStyle($style)
  {
    if (is_numeric($style))
    {
      $this->style = (int)$style;

      return true;
    }

    return false;
  }

  /**
   * Gets the style
   *
   * @return integer
   */
  public function getStyle()
  {
    return $this->style;
  }

  /**
   * Apply the transform to the sfImage object.
   *
   * @param object
   * @return object
   */
  protected function transform(sfImage $image)
  {
    $resource = $image->getAdapter()->getHolder();

    imagesetthickness($resource, $this->thickness);

    if (!is_null($this->fill))
    {
      if (!is_object($this->fill))
      {
        imagefilledarc($resource, $this->x, $this->y, $this->width, $this->height, $this->start_angle, $this->end_angle, $image->getAdapter()->getColorByHex($resource, $this->fill), $this->style);
      }

      if ($this->color !== "" && $this->fill !== $this->color)
      {
        imagearc($resource, $this->x, $this->y, $this->width, $this->height, $this->start_angle, $this->end_angle, $image->getAdapter()->getColorByHex($resource, $this->color));
      }

    }

    else
    {

      imagearc($resource, $this->x, $this->y, $this->width, $this->height, $this->start_angle, $this->end_angle, $image->getAdapter()->getColorByHex($resource, $this->color));
    }

    return $image;
  }
}
