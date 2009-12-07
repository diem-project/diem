<?php
/*
 * This file is part of the sfImageTransform package.
 * (c) 2007 Stuart Lowes <stuart.lowes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * sfImageBorderGeneric class
 *
 * draws a basic border around the image
 *
 * @author Stuart Lowes <stuart.lowes@gmail.com>
 * @author Miloslav Kmet <miloslav.kmet@gmail.com>
 * 
 * @package sfImageTransform
 * @subpackage transforms
 * @author Stuart Lowes <stuart.lowes@gmail.com>
 * @version SVN: $Id$
 */
class sfImageBorderGeneric extends sfImageTransformAbstract
{

  /**
   * thickness of the border
   */
  protected $thickness = 1;

  /**
   * Hex color.
   *
   * @var string
  */
  protected $color = '';

  /**
   * Construct an sfImageBorderGeneric object.
   *
   * @param integer
   * @param string
   */
  public function __construct($thickness, $color=null)
  {
    $this->setThickness($thickness);
    $this->setColor($color);
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
   * Sets the border color in hex
   *
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
   * Apply the transformation to the image and returns the image thumbnail
   */
  protected function transform(sfImage $image)
  {
  
    // Work out where we need to draw to
    $offset = $this->getThickness() / 2;
    $mod = $this->getThickness() % 2;
    
    $x2 = $image->getWidth() - $offset - ($mod === 0 ? 1 : 0);
    $y2 = $image->getHeight() - $offset - ($mod === 0 ? 1 : 0);
    
    $image->rectangle($offset, $offset, $x2, $y2, $this->getThickness(), $this->getColor());
  
    return $image;
  }
}
