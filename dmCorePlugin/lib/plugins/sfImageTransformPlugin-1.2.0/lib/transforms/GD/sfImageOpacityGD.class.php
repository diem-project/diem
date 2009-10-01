<?php
/*
 * This file is part of the sfImageTransform package.
 * (c) 2007 Stuart <stuart.lowes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * sfImageOpacityImageMagick class
 *
 * Changes the opacity of an image
 *
 * @package sfImageTransform
 * @subpackage transforms
 * @author Stuard Lowes <stuart.lowes@gmail.com>
 * @author Miloslav Kmet <miloslav.kmet@gmail.com>
 * @version SVN: $Id$
 */
class sfImageOpacityGD extends sfImageTransformAbstract
{
  /**
   * The opacity applied to the image
   */
  protected $opacity = 1;

  /**
   * Constructor of an sfImageOpacity transformation
   *
   * @param float $opacity If greater than 1, will be divided by 100
   */
  public function __construct($opacity)
  {
    $this->setOpacity($opacity);
  }

  /**
   * sets the opacity
   * @param float $opacity Image between 0 and 1. If $opacity > 1, will be diveded by 100
   * @return void
   */
  public function setOpacity($opacity)
  {
    if (is_numeric($opacity) or is_float($opacity))
    {
      if ($opacity < 1)
      {
        $this->opacity  = $opacity * 100;
      }

      else
      {
        $this->opacity = $opacity;
      }
      $this->opacity   = 100 - $opacity;
    }
  }

  /**
   * returns the current opacity
   *
   * @return float opacity
   */
  public function getOpacity()
  {
    return $this->opacity;
  }

  /**
   * Apply the opacity transformation to the sfImage object
   *
   * @param sfImage
   *
   * @return sfImage
   */
  protected function transform(sfImage $image)
  {
    $new_img  = $image->getAdapter()->getTransparentImage($image->getWidth(), $image->getHeight());

    imagealphablending($new_img, false);
    imagesavealpha($new_img, true);

    $opacity = (int)round(127-((127/100)*$this->getOpacity()));

    // imagesavealpha($new_img, true);
    $width  = $image->getWidth();
    $height = $image->getHeight();

    for ($x=0;$x<$width; $x++)
    {
      for ($y=0;$y<$height; $y++)
      {
        $rgb = imagecolorat($image->getAdapter()->getHolder(), $x, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        $alpha = ($rgb & 0x7F000000) >> 24;

        $new_opacity = ($alpha + ((127-$alpha)/100)*$this->getOpacity());

        $colors[$alpha] = $new_opacity;

        $color = imagecolorallocatealpha($new_img, $r, $g, $b, $new_opacity);
        imagesetpixel($new_img,$x, $y, $color);
      }
    }

    $image->getAdapter()->setHolder($new_img);

    return $image;
  }
}
