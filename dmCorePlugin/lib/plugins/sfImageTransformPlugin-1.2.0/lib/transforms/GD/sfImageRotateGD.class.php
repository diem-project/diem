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
 * sfImageResizeTD class.
 *
 * Rotates a GD image.
 *
 * Rotates image by a set angle.
 *
 * @package sfImageTransform
 * @subpackage transforms
 * @author Stuart Lowes <stuart.lowes@gmail.com>
 * @version SVN: $Id$
 */
class sfImageRotateGD extends sfImageTransformAbstract
{
  /**
   * Angle to rotate
   *
   * @param integer
   */
  protected $angle;

  /**
   * Background color.
   *
   * @param integer
   */
  protected $background = '';
  /**
   * Construct an sfImageCrop object.
   *
   * @param integer
   * @param string
   */
  public function __construct($angle, $background='')
  {
    $this->setAngle($angle);
    $this->setBackgroundColor($background);
  }

  /**
   * set the angle to rotate the image by.
   *
   * @param integer
   */
  public function setAngle($angle)
  {
    $this->angle = $angle;
  }

  /**
   * Gets the angle to rotate the image by.
   *
   * @return integer
   */
  public function getAngle()
  {
    return $this->angle;
  }

  /**
   * set the background color for the image.
   *
   * @param integer
   */
  public function setBackgroundColor($color)
  {
    $this->background = $color;
  }

  /**
   * Gets the angle to rotate the image by.
   *
   * @return integer
   */
  public function getBackgroundColor()
  {
    return $this->background;
  }

  /**
   * Apply the transform to the sfImage object.
   *
   * @param sfImage
   * @return sfImage
   */
  protected function transform(sfImage $image)
  {
    // No need to do anything
    if ($this->angle == 0)
    {
      return $image;
    }

    $resource = $image->getAdapter()->getHolder();

    // By default use the background of the top left corner
    if ($this->background === '')
    {
      $this->background = ImageColorAt($resource, 0, 0);
    }

    else
    {
      $this->background = $image->getAdapter()->getColorByHex($resource, $this->background);
    }

    // Easy
    if (function_exists("imagerotate"))
    {
      $image->getAdapter()->setHolder(imagerotate($resource, $this->angle, $this->background));
    }

    // Manual
    // manual rotating based base on pilot at myupb dot com @ php.net
    else
    {
      throw new sfImageTransformException(sprintf('Cannot perform transform: %s. Your install of GD does not support imagerotate',get_class($this)));

      // TODO: FIX ME!!

      $srcw = imagesx($resource);
      $srch = imagesy($resource);

      // Convert the angle to radians
      $pi = 3.141592654;
      $theta = $this->angle * $pi / 180;

      // Get the origin (center) of the image
      $originx = $srcw / 2;
      $originy = $srch / 2;

      // The pixels array for the new image
      $pixels = array();
      $minx = 0;
      $maxx = 0;
      $miny = 0;
      $maxy = 0;
      $dstw = 0;
      $dsth = 0;

      // Loop through every pixel and transform it
      for ($x=0;$x<$srcw;$x++)
      {

        for ($y=0;$y<$srch;$y++)
        {

          list($x1, $y1) = $this->translateCoordinate($originx, $originy, $x, $y, false);

          $x2 = $x * cos($theta) - $y * sin($theta);
          $y2 = $x * sin($theta) + $y * cos($theta);

          // Store the pixel color
          $pixels[] = array($x2, $y2, imagecolorat($resource, $x, $y));

          // Check our boundaries
          if ($x2 > $maxx)
          {
            $maxx = $x2;
          }

          if ($x2 < $minx)
          {
            $minx = $x2;
          }

          if ($y2 > $maxy)
          {
            $maxy = $y2;
          }

          if ($y2 < $miny)
          {
            $miny = $y2;
          }
        }
      }

      // Determine the new image size
      $dstw = $maxx - $minx + 1;
      $dsth = $maxy - $miny + 1;

      // Create our new image
      $dstImg = $image->getAdapter()->getTransparentImage($dstw, $dsth);

      // Get the new origin
      $neworiginx = -$minx;
      $neworiginy = -$miny;

      // Fill in the pixels
      foreach($pixels as $data)
      {
        list($x, $y, $color) = $data;
        list($newx, $newy) = $this->translateCoordinate($neworiginx, $neworiginy, $x, $y);

        imagesetpixel($dstImg, (int)$newx, (int)$newy, $color);
      }

      unset($resource);
      $image->getAdapter()->setHolder($dstImg);

    }

    return $image;
  }

  protected function translateCoordinate($originx, $originy, $x, $y, $toComp=true)
  {
    if ($toComp)
    {
      $newx = $originx + $x;
      $newy = $originy - $y;
    }

    else
    {
      $newx = $x - $originx;
      $newy = $originy - $y;
    }

    return array($newx, $newy);
  }
}
