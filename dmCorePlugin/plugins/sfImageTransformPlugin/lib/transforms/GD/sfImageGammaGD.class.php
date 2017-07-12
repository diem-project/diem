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
 * sfImageGammaGD class.
 *
 * Apply a gamma correction to a GD image.
 *
 * @package sfImageTransform
 * @subpackage transforms
 * @author Stuart Lowes <stuart.lowes@gmail.com>
 * @version SVN: $Id$
 */
class sfImageGammaGD extends sfImageTransformAbstract
{
  /**
   * The input gamma.
   * @var float
  */
  protected $input_gamma = 1.0;

  /**
   * The number of pixels used for the blur.
   * @var float
  */
  protected $output_gamma = 1.6;

  /**
   * Construct an sfImageGamma object.
   *
   * @param float
   * @param float
   */
  public function __construct($input_gamma=1.0, $output_gamma=1.6)
  {
    $this->setInputGamma($input_gamma);
    $this->setOutputGamma($output_gamma);
  }

  /**
   * Sets the input gamma
   *
   * @param float
   * @return boolean
   */
  public function setInputGamma($gamma)
  {
    if (is_float($gamma))
    {
      $this->input_gamma = (float)$gamma;

      return true;
    }

    return false;
  }

  /**
   * Gets the input gamma
   *
   * @return integer
   */
  public function getInputGamma()
  {
    return $this->input_gamma;
  }

  /**
   * Sets the ouput gamma
   *
   * @param float
   */
  public function setOutputGamma($gamma)
  {
    if (is_numeric($gamma))
    {
      $this->ouput_gamma = (float)$gamma;

      return true;
    }
  }

  /**
   * Gets the ouput gamma
   *
   * @return integer
   */
  public function getOuputGamma()
  {
    return $this->ouput_gamma;
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
    imagegammacorrect ($resource, $this->input_gamma, $this->output_gamma);

    return $image;
  }
}
