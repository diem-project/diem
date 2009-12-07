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
 * sfImageTransform class.
 *
 * Abstract class.
 *
 * Abstract class all sfImageTranform transform classes are extended from.
 *
 * @abstract
 * @package sfImageTransform
 * @subpackage transforms
 * @author Miloslav Kmet <miloslav.kmet@gmail.com>
 * @author Stuart Lowes <stuart.lowes@gmail.com>
 * @version SVN: $Id$
 */
abstract class sfImageTransformAbstract 
{

  /**
   * Apply the transform to the sfImage object.
   *
   * @param sfImage
   * @return sfImage
   */
  public function execute(sfImage $image)
  {

    // Check we have a valid image holder
    if(false === $image->getAdapter()->hasHolder())
    {
      throw new sfImageTransformException(sprintf('Cannot perform transform: %s invalid image resource',get_class($this)));
    }
    return $this->transform($image);
  }

  /**
   * Abstract method that performs the image manipulation.
   *
   * @param sfImage
   * @ignore
   * @return sfImage
   */
  abstract protected function transform(sfImage $image);

}
