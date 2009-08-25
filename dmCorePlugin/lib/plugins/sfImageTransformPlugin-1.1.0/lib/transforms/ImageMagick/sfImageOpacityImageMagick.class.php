<?php
/*
 * This file is part of the sfImageTransform package.
 * (c) 2007 Stuart <stuart.lowes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * sfImageOpacityImageMagick class
 *
 * Changes the opacity of an image
 *
 * @package sfImageTransform
 * @subpackage transforms
 * @author Stuard Lowes <stuart.lowes@gmail.com>
 * @author Miloslav Kmet <miloslav.kmet@gmail.com>
 *
 * @version SVN: $Id$
 */
class sfImageOpacityImageMagick extends sfImageTransformAbstract
{
  /**
   * The opacity applied to the image
   */
  protected $opacity = 1;

  /**
   * Constructor of an sfImageOpacity transformation
   *
   * @param float $opacity If greater than 1, will be divided by 100
   */
  public function __construct($opacity)
  {
    $this->setOpacity($opacity);
  }

  /**
   * sets the opacity
   * @param float $opacity Image between 0 and 1. If $opacity > 1, will be diveded by 100
   * @return void
   */
  public function setOpacity($opacity)
  {
    if (is_numeric($opacity) or is_float($opacity))
    {
      if ($opacity <= 1)
      {
        $this->opacity  = $opacity;
      }

      else
      {
        $this->opacity = $opacity/100;
      }
    }
  }

  /**
   * returns the current opacity
   *
   * @return float opacity
   */
  public function getOpacity()
  {
    return $this->opacity;
  }

  /**
   * Apply the opacity transformation to the sfImage object
   *
   * @param sfImage
   *
   * @return sfImage
   */
  protected function transform(sfImage $image)
  {
    $image->getAdapter()->getHolder()->setImageOpacity($this->getOpacity());

    return $image;
  }
}
