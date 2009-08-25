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
 * sfImagePixelizeGD class.
 *
 * Pixelizes a GD image.
 *
 * Reduces the level of detail of a GD image.
 *
 * @package sfImageTransform
 * @subpackage transforms
 * @author Stuart Lowes <stuart.lowes@gmail.com>
 * @version SVN: $Id$
 */
class sfImagePixelizeGD extends sfImageTransformAbstract
{
  /**
   * The size of the pixelization.
  */
  protected $block_size = 10;

  /**
   * Construct an sfImagePixelize object.
   *
   * @param array integer
   */
  public function __construct($size=10)
  {
    $this->setSize($size);
  }

  /**
   * Set the pixelize blocksize.
   *
   * @param integer
   * @return boolean
   */
  public function setSize($pixels)
  {
    if (is_numeric($pixels) && $pixels > 0)
    {
      $this->block_size = (int)$pixels;

      return true;
    }

    return false;
  }

  /**
   * Returns the pixelize blocksize.
   *
   * @return integer
   */
  public function getSize()
  {
    return $this->block_size;
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

    $resourcex = imagesx($resource);
    $resourcey = imagesy($resource);

    for ($x = 0; $x < $resourcex; $x += $this->block_size)
    {
      for ($y = 0; $y < $resourcey; $y += $this->block_size)
      {
        $rgb = imagecolorat($resource, $x, $y);
        imagefilledrectangle($resource, $x, $y, $x + $this->block_size - 1, $y + $this->block_size - 1, $rgb);
      }
    }

    return $image;
  }
}
