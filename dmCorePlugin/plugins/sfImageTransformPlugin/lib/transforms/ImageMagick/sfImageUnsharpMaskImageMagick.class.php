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
 * sfImageUnsharpMaskGD class.
 *
 * Applies an unsharp mask to the image.
 *
 * Based on http://vikjavev.no/computing/ump.php
 *
 * @package sfImageTransform
 * @subpackage transforms
 * @author Stuart Lowes <stuart.lowes@gmail.com>
 * @version SVN: $Id$
 */
class sfImageUnsharpMaskImageMagick extends sfImageTransformAbstract
{
  /**
   *
   * @var float
   */
  protected $amount = null;

  /**
   *
   * @var float
   */
  protected $radius = null;

  /**
   *
   * @var float
   */
  protected $threshold = null;

  /**
   *
   * @var float
   */
  protected $sigma = null;

  /**
   *
   * Channel
   *
   * @var integer
   */
  protected $channel = Imagick::CHANNEL_ALL;


  public function __construct($radius, $threshold, $amount, $sigma, $channel = Imagick::CHANNEL_ALL)
  {
    $this->setRadius($radius);
    $this->setThreshold($threshold);
    $this->setAmount($amount);
    $this->setSigma($sigma);
  }

  /**
   * The radius of the blurring circle of the mask
   *
   * @param integer
   */
  public function setRadius($radius)
  {
    if(is_numeric($radius))
    {

      $this->radius = (float)$radius;

      return true;
    }

    return false;
  }

  /**
   * Returns the radius of the blurring circle of the mask
   *
   * @return integer
   */
  public function getRadius()
  {
    return $this->radius;
  }

  /**
   * Threshold the least
   * difference in colour values that is allowed between the original and the mask. In practice
   * this means that low-contrast areas of the picture are left unrendered whereas edges
   * are treated normally. This is good for pictures of e.g. skin or blue skies.
   *
   * @param float
   */
  public function setThreshold($threshold)
  {
    if(is_numeric($threshold))
    {
      $this->threshold = $threshold;

      return true;
    }

    return false;
  }

  /**
   *
   *
   * @return integer
   */
  public function getThreshold()
  {
    return $this->threshold;
  }

  /**
   * Amount of unsharp to be applied. 100 is normal
   *
   * @param integer
   */
  public function setAmount($amount)
  {
    if(is_numeric($amount))
    {
      $this->amount = (float)$amount;

      return true;
    }

    return false;
  }

  /**
   * Returns the amount of unsharp mask to be applied
   *
   * @return integer
   */
  public function getAmount()
  {
    return $this->amount;
  }

  /**
   *
   *
   * @param float
   */
  public function setSigma($sigma)
  {
    if(is_numeric($sigma))
    {
      $this->sigma = (float)$sigma;
      
      return true;
    }

    return false;
  }

  /**
   *
   *
   * @return float
   */
  public function getSigma()
  {
    return $this->sigma;
  }

  /**
   *
   *
   * @param integer
   */
  public function setChannel($channel)
  {
    $this->channel = $channel;
  }

  /**
   *
   *
   * @return integer
   */
  public function getChannel()
  {
    return $this->channel;
  }
  
  protected function transform(sfImage $image) 
  {
    $resource = $image->getAdapter()->getHolder();

    $resource->normalizeImage();

    $resource->unsharpMaskImage($this->getRadius(), $this->getSigma(), $this->getAmount(), $this->getThreshold());

    return $image;
  }
}
