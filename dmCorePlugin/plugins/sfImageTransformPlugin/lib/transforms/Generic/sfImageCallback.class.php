<?php
/*
 * This file is part of the ImageTransform package.
 * (c) 2009 Stuart Lowes <stuart.lowes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * sfImageCallbackGeneric class
 *
 * Callback transform
 *
 * Allows the calling of external methods or functions as a transform
 *
 * @package sfImageTransform
 * @subpackage transforms
 * @author Stuart Lowes <stuart.lowes@gmail.com>
 * @version SVN: $Id$
 */
class sfImageCallbackGeneric extends sfImageTransformAbstract
{

  /**
   * Callback function or class/object method.
   * @access protected
   * @var object
  */
  protected $function = null;

  /**
   * Any arguments for the callback function.
   * @access protected
   * @var object
  */
  protected $arguments = null;

  /**
   * constructor
   *
   * @param integer $width of the thumbnail
   * @param integer $height of the thumbnail
   * @param boolean could the target image be larger than the source ?
   * @param boolean should the target image keep the source aspect ratio ?
   *
   * @return void
   */
  public function __construct($function, $arguments = null)
  {
    $this->setFunction($function);
    $this->setArguments($arguments);

  }

  /**
   *
   * @param mixed $function
   * @return boolean
   */
  public function setFunction($function)
  {
    if(is_callable($function))
    {
      $this->function = $function;

      return true;
    }

    throw new sfImageTransformException(sprintf('Callback method does not exist'));
  }

  /**
   *
   * @return mixed
   */
  public function getFunction()
  {
    return $this->function;
  }


  /**
   *
   * @param mixed $arguments
   */
  public function setArguments($arguments)
  {
    $this->arguments = $arguments;
  }

  /**
   *
   * @return mixed
   */
  public function getArguments()
  {
    return $this->arguments;
  }

  /**
   *
   * @param sfImage $image
   * @return sfImage
   */
  public function transform(sfImage $image)
  {
    call_user_func_array($this->getFunction(), array('image' => $image, 'arguments' => $this->getArguments()));

    return $image;
  }
}