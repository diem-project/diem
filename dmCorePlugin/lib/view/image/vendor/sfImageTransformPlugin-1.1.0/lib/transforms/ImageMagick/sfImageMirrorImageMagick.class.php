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
 * sfImageMirrorImageMagick class.
 *
 * Mirrors a ImageMagick image.
 *
 * Creates a mirror image of the original image.
 *
 * @package sfImageTransform
 * @subpackage transforms
 * @author Stuart Lowes <stuart.lowes@gmail.com>
 * @version SVN: $Id$
 */
class sfImageMirrorImageMagick extends sfImageTransformAbstract
{
  /**
   * Apply the transform to the sfImage object.
   *
   * @param integer
   * @return sfImage
   */
  protected function transform(sfImage $image)
  {
    // Check we have a valid image resource
    $resource = $image->getAdapter()->getHolder();

    $resource->flopImage();

    return $image;
  }
}
