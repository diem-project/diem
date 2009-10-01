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
 * sfImageSmoothGD class.
 *
 * Greyscales an image.
 *
 * Reduces the level of detail of an image.
 *
 * @package sfImageTransform
 * @subpackage transforms
 * @author Stuart Lowes <stuart.lowes@gmail.com>
 * @version SVN: $Id$
 */
class sfImageSmoothGD extends sfImageTransformAbstract
{
  /**
   * Smoothness level to be applied.
  */
  protected $smoothness = 0;

  /**
   * Construct an sfImageSmooth object.
   *
   * @param integer
   */
  public function __construct($smoothness=0)
  {
    $this->setSmoothness($smoothness);
  }

  /**
   * Sets the smoothness
   *
   * @param integer
   * @return boolean
   */
  public function setSmoothness($smoothness)
  {
    if (is_numeric($smoothness))
    {
      $this->smoothness = (int)$smoothness;

      return true;
    }

    return false;
  }

  /**
   * Gets the smoothness
   *
   * @return integer
   */
  public function getSmoothness()
  {
    return $this->smoothness;
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

    if (function_exists('imagefilter'))
    {
      imagefilter($resource, IMG_FILTER_SMOOTH, $this->smoothness);
    }

    else
    {
      throw new sfImageTransformException(sprintf('Cannot perform transform, GD does not support imagefilter '));
    }

    return $image;
  }
}
