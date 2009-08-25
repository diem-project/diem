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
 * sfImageContrastGD class.
 *
 * Sets the contrast of an GD image.
 *
 * Reduces the level of detail of an GD image.
 *
 * @package sfImageTransform
 * @subpackage transforms
 * @author Stuart Lowes <stuart.lowes@gmail.com>
 * @version SVN: $Id$
 */
class sfImageContrastGD extends sfImageTransformAbstract
{
  /**
   * Constract level to be applied.
  */
  protected $contrast = 0;

  /**
   * Construct an sfImageContrast object.
   *
   * @param integer
   */
  public function __construct($contrast)
  {
    $this->setContrast($contrast);
  }

  /**
   * Sets the contrast
   *
   * @param integer
   * @return boolean
   */
  public function setContrast($contrast)
  {
    if (is_numeric($contrast))
    {
      $this->contrast = (int)$contrast;

      return true;
    }

    return false;
  }

  /**
   * Gets the contrast
   *
   * @return integer
   */
  public function getContrast()
  {
    return $this->contrast;
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
      imagefilter($resource, IMG_FILTER_CONTRAST, $this->contrast);
    }

    else
    {
      throw new sfImageTransformException(sprintf('Cannot perform transform, GD does not support imagefilter '));
    }

    return $image;
  }

}
