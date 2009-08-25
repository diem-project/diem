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
 * sfImageBrightnessGD class.
 *
 * Sets the brightness of a GD image.
 *
 * @package sfImageTransform
 * @subpackage transforms
 * @author Stuart Lowes <stuart.lowes@gmail.com>
 * @version SVN: $Id$
 */
class sfImageBrightnessGD extends sfImageTransformAbstract
{
  /**
   * Constract level to be applied.
  */
  protected $brightness = 0;

  /**
   * Construct an sfImageBrightness object.
   *
   * @param integer
   */
  public function __construct($brightness)
  {
    $this->setBrightness($brightness);
  }

  /**
   * Sets the brightness
   *
   * @param integer
   * @return boolean
   */
  public function setBrightness($brightness)
  {
    if (is_numeric($brightness))
    {
      $this->brightness = (int)$brightness;

      return true;
    }

    return false;
  }

  /**
   * Gets the brightness
   *
   * @return integer
   */
  public function getBrightness()
  {
    return $this->brightness;
  }

  /**
   * Apply the transform to the sfImage object.
   *
   * @access protected
   * @param sfImage
   * @return sfImage
   */
  protected function transform(sfImage $image)
  {
    $resource = $image->getAdapter()->getHolder();

    if (function_exists('imagefilter'))
    {
      imagefilter($resource, IMG_FILTER_BRIGHTNESS, $this->brightness);
    }

    else
    {
      throw new sfImageTransformException(sprintf('Cannot perform transform, GD does not support imagefilter '));
    }

    return $image;
  }

}
