<?php
/**
 * This file is part of the sfImageTransformExtraPlugin package.
 * (c) 2010 Christian Schaefer <caefer@ical.ly>>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    sfImageTransformExtraPlugin
 * @author     Christian Schaefer <caefer@ical.ly>
 * @version    SVN: $Id: sfRawFileCache.class.php 63 2010-03-09 04:34:28Z caefer $
 */

/**
 * Image transformation to apply rounded corners to the image
 *
 * @package    sfImageTransformExtraPlugin
 * @subpackage transforms
 * @author     Christian Schaefer <caefer@ical.ly>
 */
class sfImageRoundedCornersGD extends sfImageAlphaMaskGD
{

  protected $radius = 5;

  public function __construct($radius, $color = false) 
  {
    $this->setRadius($radius);
    $this->setColor($color);
  }
  
  protected function transform(sfImage $image) 
  {    
    $this->setMask($this->createMask($image, $image->getWidth(), $image->getHeight()));
    
    return parent::transform($image);
  }

  public function setRadius($radius)
  {
    if(is_numeric($radius) && $radius > 0)
    {
      $this->radius = $radius;

      return true;
    }

    return false;
  }

  public function getRadius()
  {
    return $this->radius;
  }

  protected function createMask(sfImage $image, $w, $h)
  {
    // Create a mask png image of the area you want in the circle/ellipse (a 'magicpink' image with a black shape on it, with black set to the colour of alpha transparency) - $mask
    $mask = $image->getAdapter()->getTransparentImage($w, $h);

    // Set the masking colours
    if (false === $this->getColor() ||'image/png' == $image->getMIMEType())
    {
      $mask_black = imagecolorallocate($mask, 0, 0, 0);
    }
    else
    {
      $mask_black = $image->getAdapter()->getColorByHex($mask, $this->getColor());
    }

    // Cannot use white as transparent mask if color is set to white
    if($this->getColor() === '#FFFFFF' || $this->getColor() === false)
    {
      $mask_transparent = imagecolorallocate($mask, 255, 0, 0);
    }

    else
    {
      $mask_color = imagecolorsforindex($mask, imagecolorat($image->getAdapter()->getHolder(), 0, 0));
      $mask_transparent = imagecolorallocate($mask, $mask_color['red'], $mask_color['green'], $mask_color['blue']);
    }
    
    imagecolortransparent($mask, $mask_transparent);
    imagefill($mask, 0, 0, $mask_black);

    // Draw the rounded rectangle for the mask
    $this->imagefillroundedrect($mask, 0, 0, $w, $h, $this->getRadius(), $mask_transparent);

    $mask_image = clone $image;
    $mask_image->getAdapter()->setHolder($mask);

    return $mask_image;
  }
  
  protected function imagefillroundedrect($im, $x, $y, $cx, $cy, $rad, $col)
  {
    // Draw the middle cross shape of the rectangle
    imagefilledrectangle($im, $x, $y + $rad, $cx, $cy - $rad, $col);
    imagefilledrectangle($im, $x + $rad, $y, $cx - $rad, $cy, $col);
    
    $dia = $rad * 2;
    
    // Now fill in the rounded corners
    imagefilledellipse($im, $x + $rad, $y + $rad, $rad * 2, $dia, $col);
    imagefilledellipse($im, $x + $rad, $cy - $rad, $rad * 2, $dia, $col);
    imagefilledellipse($im, $cx - $rad, $cy - $rad, $rad * 2, $dia, $col);
    imagefilledellipse($im, $cx - $rad, $y + $rad, $rad * 2, $dia, $col);
  }
}
