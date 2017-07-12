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
 * Image transformation to apply a second image as an alpha mask to the first image
 *
 * @package    sfImageTransformExtraPlugin
 * @subpackage transforms
 * @author     Christian Schaefer <caefer@ical.ly>
 */
class sfImageAlphaMaskGD extends sfImageTransformAbstract
{
  /**
   * sfImage mask object
   * 
   * @var sfImage
   */
  protected $mask = null;

  /**
   * Color
   *
   * @var mixed
   */
  protected $color = false;

  public function __construct($mask, $color = false) 
  {
    $this->setMask($mask);
    $this->setColor($color);
  }

  public function setMask(sfImage $mask)
  {
    $this->mask = $mask;

    return true;
  }

  public function getMask()
  {
    return $this->mask;
  }

  public function setColor($color)
  {
    if (preg_match('/#[\d\w]{6}/',$color))
    {
      $this->color = strtoupper($color);

      return true;
    }

    return false;
  }

  public function getColor()
  {
    return $this->color;
  }
  
  protected function transform(sfImage $image) 
  {

    switch ($image->getMIMEType())
    {
      case 'image/png':
        $this->transformAlpha($image);
        break;
      case 'image/gif':
      case 'image/jpg':
      default:
        $this->transformDefault($image);
    }
    
    return $image;
  }

  private function transformAlpha(sfImage $image)
  {
    $w = $image->getWidth();
    $h = $image->getHeight();

    $resource = $image->getAdapter()->getHolder();
    
    $canvas = imagecreatetruecolor($w, $h);

    $mask = $this->getMask()->getAdapter()->getHolder();
    
    $color_background = imagecolorallocate($canvas, 0, 0, 0);
    imagefilledrectangle($canvas, 0, 0, $w, $h, $color_background);
    imagealphablending($canvas, false);
    imagesavealpha($canvas, true);
    
    for ($x = 0;$x < $w;$x++) 
    {
      for ($y = 0;$y < $h;$y++) 
      {
        $real_pixel = imagecolorsforindex($resource, imagecolorat($resource, $x, $y));
        $mask_pixel = imagecolorsforindex($mask, imagecolorat($mask, $x, $y));
        $mask_alpha = 127 - (floor($mask_pixel['red'] / 2) * (1 - ($real_pixel['alpha'] / 127)));
        
        if (false === $this->getColor())
        {
          $newcolor = imagecolorallocatealpha($canvas, $real_pixel['red'], $real_pixel['green'], $real_pixel['blue'], intval($mask_alpha));
        }
        else
        {
          $newcolorPixel    = sscanf($this->getColor(), '#%2x%2x%2x');
          $newcolorPixel[0] = ($newcolorPixel[0] * $mask_alpha + $real_pixel['red'] * (127 - $mask_alpha)) / 127;
          $newcolorPixel[1] = ($newcolorPixel[1] * $mask_alpha + $real_pixel['green'] * (127 - $mask_alpha)) / 127;
          $newcolorPixel[2] = ($newcolorPixel[2] * $mask_alpha + $real_pixel['blue'] * (127 - $mask_alpha)) / 127;
          $newcolor         = imagecolorallocate($canvas, $newcolorPixel[0], $newcolorPixel[1], $newcolorPixel[2]);
        }
        
        imagesetpixel($canvas, $x, $y, $newcolor);
      }
    }

    imagealphablending($resource, false);
    imagesavealpha($resource, true);
    imagecopy($resource, $canvas, 0, 0, 0, 0, $w, $h);
    
    imagedestroy($canvas);
  }
  
  protected function transformDefault(sfImage $image)
  {
    $w = $image->getWidth();
    $h = $image->getHeight();

    $resource = $image->getAdapter()->getHolder();

    $mask = $this->getMask()->getAdapter()->getHolder();
    
    imagealphablending($resource, true);
    $resource_transparent = imagecolorallocate($resource, 0, 0, 0);
    imagecolortransparent($resource, $resource_transparent);
    
    // Copy $mask over the top of $resource maintaining the Alpha transparency
    imagecopymerge($resource, $mask, 0, 0, 0, 0, $w, $h, 100);
  }
}
