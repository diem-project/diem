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
 * sfImageLineGD class.
 *
 * Draws a line on a GD.
 *
 * @package sfImageTransform
 * @subpackage transforms
 * @author Stuart Lowes <stuart.lowes@gmail.com>
 * @version SVN: $Id$
 */
class sfImageLineGD extends sfImageTransformAbstract
{
  /**
   * Start X coordinate.
  */
  protected $x1 = 0;

  /**
   * Start Y coordinate.
  */
  protected $y1 = 0;

  /**
   * Finish X coordinate.
  */
  protected $x2 = 0;

  /**
   * Finish Y coordinate
  */
  protected $y2 = 0;

  /**
   * Line thickness.
  */
  protected $thickness = 1;

  /**
   * Hex color.
  */
  protected $color = '#000000';

  /**
   * The number of pixels used for the blur.
  */
  protected $style = null;

  /**
   * Construct an sfImageBlur object.
   *
   * @param array integer
   */
  public function __construct($x1, $y1, $x2, $y2, $thickness=1, $color='#000000', $style=null)
  {
    $this->setStartX($x1);
    $this->setStartY($y1);
    $this->setEndX($x2);
    $this->setEndY($y2);
    $this->setThickness($thickness);
    $this->setColor($color);
    $this->setStyle($style);
  }

  /**
   * Sets the start X coordinate
   *
   * @param integer
   * @return boolean
   */
  public function setStartX($x)
  {
    if (is_numeric($x))
    {
      $this->x1 = (int)$x;

      return true;
    }

    return false;
  }

  /**
   * Gets the start X coordinate
   *
   * @return integer
   */
  public function getStartX()
  {
    return $this->x1;
  }

  /**
   * Sets the start Y coordinate
   *
   * @param integer
   * @return boolean
   */
  public function setStartY($y)
  {
    if (is_numeric($y))
    {
      $this->y1 = (int)$y;

      return true;
    }

    return false;
  }

  /**
   * Gets the Y coordinate
   *
   * @return integer
   */
  public function getStartY()
  {
    return $this->y1;
  }

  /**
   * Sets the end X coordinate
   *
   * @param integer
   * @return boolean
   */
  public function setEndX($x)
  {
    if (is_numeric($x))
    {
      $this->x2 = (int)$x;

      return true;
    }

    return false;
  }

  /**
   * Gets the end X coordinate
   *
   * @return integer
   */
  public function getEndX()
  {
    return $this->x2;
  }

  /**
   * Sets the end Y coordinate
   *
   * @param integer
   * @return boolean
   */
  public function setEndY($y)
  {
    if (is_numeric($y))
    {
      $this->y2 = (int)$y;

      return true;
    }

    return false;
  }

  /**
   * Gets the end Y coordinate
   *
   * @return integer
   */
  public function getEndY()
  {
    return $this->y2;
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
   * Sets the style
   *
   * @param integer
   * @return boolean
   */
  public function setStyle($style)
  {
    if (is_numeric($style = $style))
    {
      $this->style = $style;

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
   * @param sfImage
   * @return sfImage
   */
  protected function transform(sfImage $image)
  {
    $resource = $image->getAdapter()->getHolder();

    if (!is_null($this->style))
    {
      imagesetstyle($this->style);
    }

    if ($this->thickness === 1)
    {
      imageline($resource, $this->x1, $this->y1, $this->x2, $this->y2, $image->getAdapter()->getColorByHex($resource, $this->color));
    }

    else if ($this->x1 === $this->x2 || $this->y1 === $this->y2)
    {
      imagefilledrectangle($resource, round(min($this->x1, $this->x2) - $this->thickness), round(min($this->y1, $this->y2) - $this->thickness), round(max($this->x1, $this->x2) + $this->thickness), round(max($this->y1, $this->y2) + $this->thickness), $image->getAdapter()->getColorByHex($resource, $this->color));
    }

    else
    {
      $k = ($this->y2 - $this->y1) / ($this->x2 - $this->x1);
      $a = $this->thickness / sqrt(1 + pow($k, 2));
      $points = array(
        round($this->x1 - (1+$k)*$a), round($this->y1 + (1-$k)*$a),
        round($this->x1 - (1-$k)*$a), round($this->y1 - (1+$k)*$a),
        round($this->x2 + (1+$k)*$a), round($this->y2 - (1-$k)*$a),
        round($this->x2 + (1-$k)*$a), round($this->y2 + (1+$k)*$a),
      );

      $color = $image->getAdapter()->getColorByHex($resource, $this->color);
      imagefilledpolygon($resource, $points, 4, $color);
      imagepolygon($resource, $points, 4, $color);
    }

    return $image;
  }
}
