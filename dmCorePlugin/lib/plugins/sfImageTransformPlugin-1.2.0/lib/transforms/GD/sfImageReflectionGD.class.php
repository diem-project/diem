<?php
/*
 * This file is part of the sfImageTransform package.
 * (c) 2007 Stuart <stuart.lowes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * sfImageReflectionGD class
 *
 * adds a mirrored reflection effect to an image
 *
 * @package sfImageTransform
 * @subpackage transforms
 * @author Stuard Lowes <stuart.lowes@gmail.com>
 * @author Colin MacDonald <colin@oneweb.co.uk>
 *
 * @version SVN: $Id$
 */
class sfImageReflectionGD extends sfImageTransformAbstract
{
	 /**
   * The reflection height for the image
   */
  protected $reflection_height = 20;

	 /**
   * The starting transparency
   */
  protected $start_transparency = 30;

  /**
   * Constructor of an sfImageReflection transformation
   *
   * @param float $reflection_height
   */
  public function __construct($reflection_height=20, $start_transparency=30)
  {
    $this->setReflectionHeight($reflection_height);
    $this->setStartTransparency($start_transparency);
  }

  /**
   * Sets the reflection height
   * @param int $reflection_height
   * @return boolean
   */
  public function setReflectionHeight($reflection_height)
  {
    if (is_numeric($reflection_height))
    {
      $this->reflection_height = (int)$reflection_height;
      return true;
    }

    return false;
  }

  /**
   * Gets the reflection height
   * @param int $reflection_height
   * @return integer
   */
  public function getReflectionHeight()
  {
    return $this->reflection_height;
  }

  /**
   * Sets the start transparency
   * @param int $start_transparency
   * @return boolean
   */
  public function setStartTransparency($start_transparency)
  {
    if (is_numeric($start_transparency))
    {
      $this->start_transparency = (int)$start_transparency;
      return true;
    }

    return false;
  }

  /**
   * Gets the start transparency
   * @param int $start_transparency
   * @return integer
   */
  public function getStartTransparency()
  {
    return $this->start_transparency;
  }

  /**
   * Apply the opacity transformation to the sfImage object
   *
   * @param sfImage
   *
   * @return sfImage
   */
  public function transform(sfImage $image)
  {

    // Get the actual image resource
    $resource = $image->getAdapter()->getHolder();

    //get the resource dimentions
    $width = $image->getWidth();
    $height = $image->getHeight();

    $reflection = $image->copy();

    $reflection->flip()->resize($width, $this->reflection_height);

    $r_resource = $reflection->getAdapter()->getHolder();

    $dest_resource = $reflection->getAdapter()->getTransparentImage($width, $height + $this->reflection_height);

    imagecopymerge($dest_resource, $resource, 0, 0, 0 ,0, $width, $height, 100);

    imagecopymerge($dest_resource, $r_resource, 0, $height, 0 ,0, $width, $this->reflection_height, 100);

    // Increments we are going to increase the transparency
    $increment = 100 / $this->reflection_height;

    // Overlay line we use to apply the transparency
    $line = imagecreatetruecolor($width, 1);

    // Use white as our overlay color
    imagefilledrectangle($line, 0, 0, $width, 1, imagecolorallocate($line, 255, 255, 255));

    $tr = $this->start_transparency;

    // Start at the bottom of the original image
    for ($i = $height; $i <= $height + $this->reflection_height; $i++)
    {

      if ($tr > 100)
      {
        $tr = 100;
      }

      imagecopymerge($dest_resource, $line, 0, $i, 0, 0, $width, 1, $tr);

      $tr += $increment;

    }

    // To set a new resource for the image object
    $image->getAdapter()->setHolder($dest_resource);

    return $image;
  }
}
