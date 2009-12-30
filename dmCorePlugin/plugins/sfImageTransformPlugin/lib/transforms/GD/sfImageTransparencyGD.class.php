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
 * sfImageTransparencyGD class.
 *
 * Sets the transparency for an image.
 *
 * Defines and sets the transparent color for the image.
 *
 * @package sfImageTransform
 * @subpackage transforms
 * @author Stuart Lowes <stuart.lowes@gmail.com>
 * @version SVN: $Id$
 */
class sfImageTransparencyGD extends sfImageTransformAbstract
{
  /**
   * The transparent color defined in hex.
  */
  protected $color = '#FFFFFF';

  /**
   * Construct an sfImageCrop object.
   *
   * @param array string
   */
  public function __construct($color)
  {
    $this->setColor($color);
  }

  /**
   * Set the color to be transparent.
   *
   * @param string
   */
  public function setColor($color)
  {
    $this->color = $color;
  }

  /**
   * Gets text color.
   *
   * @return string
   */
  public function getColor()
  {
    return $this->color;
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

    // Set the defined color as transparent
    if ($this->color !== '')
    {
      $color = $image->getAdapter()->getColorByHex($resource, $this->color);
    }

    // Or default to the color at the top left
    else
    {
      $color = imagecolorat($resource, 0, 0);
    }

    imagecolortransparent($resource,$color);

    return $image;
  }
}
