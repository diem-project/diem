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
class sfImageUnsharpMaskGD extends sfImageTransformAbstract
{
  /**
   *
   * @var integer
   */
  protected $amount = 500;

  /**
   *
   * @var integer
   */
  protected $radius = 50;

  /**
   *
   * @var float
   */
  protected $threshold = 255;

  public function __construct($radius, $threshold, $amount)
  {
    $this->setRadius($radius);
    $this->setThreshold($threshold);
    $this->setAmount($amount);
  }

  /**
   * The radius of the blurring circle of the mask
   *
   * @param integer
   */
  public function setRadius($radius)
  {
    if(is_numeric($radius) && $radius > 0 && $radius <= 50)
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
    if(is_numeric($threshold) && $threshold > 0 && $threshold <= 500)
    {
      $this->threshold = (float)$threshold;
      
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
    if(is_numeric($amount) && $amount > 0 && $amount <= 500)
    {
      $this->amount = (int)$amount;

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
  
  protected function transform(sfImage $image) 
  {
    $resource = $image->getAdapter()->getHolder();

    // Attempt to calibrate the parameters to Photoshop:
    $amount = $this->getAmount() * 0.016;
    $radius = abs(round($this->getRadius() * 2));

    $threshold = $this->getThreshold();

    $w = $image->getWidth();
    $h = $image->getHeight();

    if ($radius > 0)
    {
      $imgCanvas = imagecreatetruecolor($w, $h);
      $imgBlur = imagecreatetruecolor($w, $h);
      
      if (function_exists('imageconvolution')) 
      {
        $matrix = array(   
          array( 1, 2, 1 ),
          array( 2, 4, 2 ),
          array( 1, 2, 1 )
        );
        
        imagecopy ($imgBlur, $resource, 0, 0, 0, 0, $w, $h);
        imageconvolution($imgBlur, $matrix, 16, 0);   
      } 
      
      else
      {   

        // Move copies of the image around one pixel at the time and merge them with weight  
        // according to the matrix. The same matrix is simply repeated for higher radii.  
        for ($i = 0; $i < $radius; $i++)
        {  
          imagecopy ($imgBlur, $resource, 0, 0, 1, 0, $w - 1, $h); // left
          imagecopymerge ($imgBlur, $resource, 1, 0, 0, 0, $w, $h, 50); // right
          imagecopymerge ($imgBlur, $resource, 0, 0, 0, 0, $w, $h, 50); // center
          imagecopy ($imgCanvas, $imgBlur, 0, 0, 0, 0, $w, $h);  

          imagecopymerge ($imgBlur, $imgCanvas, 0, 0, 0, 1, $w, $h - 1, 33.33333 ); // up  
          imagecopymerge ($imgBlur, $imgCanvas, 0, 1, 0, 0, $w, $h, 25); // down  
        }  
      }  

      if ($threshold > 0)
      {
        // Calculate the difference between the blurred pixels and the original  
        // and set the pixels  
        
        // Each row
        for ($x = 0; $x < $w - 1; $x++)
        { 
          // Each pixel
          for ($y = 0; $y < $h; $y++)
          {

            $rgbOrig = ImageColorAt($resource, $x, $y);
            $rOrig = (($rgbOrig >> 16) & 0xFF);  
            $gOrig = (($rgbOrig >> 8) & 0xFF);  
            $bOrig = ($rgbOrig & 0xFF);  

            $rgbBlur = ImageColorAt($imgBlur, $x, $y);  

            $rBlur = (($rgbBlur >> 16) & 0xFF);  
            $gBlur = (($rgbBlur >> 8) & 0xFF);  
            $bBlur = ($rgbBlur & 0xFF);  

            // When the masked pixels differ less from the original  
            // than the threshold specifies, they are set to their original value.  
            $rNew = $rOrig;
            if (abs($rOrig - $rBlur) >= $threshold)
            {
              $rNew = max(0, min(255, ($amount * ($rOrig - $rBlur)) + $rOrig));
            }
            
            $gNew = $gOrig;
            if (abs($gOrig - $gBlur) >= $threshold)
            {
              $gNew = max(0, min(255, ($amount * ($gOrig - $gBlur)) + $gOrig));
            }
            
            
            $bNew = $bOrig;
            if (abs($bOrig - $bBlur) >= $threshold)   
            {
              $bNew = max(0, min(255, ($amount * ($bOrig - $bBlur)) + $bOrig));
            }

            if (($rOrig != $rNew) || ($gOrig != $gNew) || ($bOrig != $bNew)) 
            {  
              $pixCol = ImageColorAllocate($resource, $rNew, $gNew, $bNew);
              ImageSetPixel($resource, $x, $y, $pixCol);
            }  
          }  
        }  
      }  
     
      else
      {  
        // each row
        for ($x = 0; $x < $w; $x++)
        {   
          // each pixel 
          for ($y = 0; $y < $h; $y++)
          {  
            $rgbOrig = ImageColorAt($resource, $x, $y);
            $rOrig = (($rgbOrig >> 16) & 0xFF);  
            $gOrig = (($rgbOrig >> 8) & 0xFF);  
            $bOrig = ($rgbOrig & 0xFF);  

            $rgbBlur = ImageColorAt($imgBlur, $x, $y);  

            $rBlur = (($rgbBlur >> 16) & 0xFF);  
            $gBlur = (($rgbBlur >> 8) & 0xFF);  
            $bBlur = ($rgbBlur & 0xFF);  

            $rNew = ($amount * ($rOrig - $rBlur)) + $rOrig;  
            
            if($rNew > 255)
            {
              $rNew = 255;
            }  
            elseif ($rNew < 0)
            {
              $rNew = 0;
            }
            
            $gNew = ($amount * ($gOrig - $gBlur)) + $gOrig;
            
            if($gNew > 255)
            {
              $gNew = 255;
            }  
            elseif ($gNew < 0)
            {
              $gNew = 0;
            }
            
            $bNew = ($amount * ($bOrig - $bBlur)) + $bOrig;  
            
            if ($bNew > 255)
            {
              $bNew = 255;
            }  
            elseif ($bNew < 0)
            {
              $bNew = 0;
            }
            
            $rgbNew = ($rNew << 16) + ($gNew <<8) + $bNew;  
            ImageSetPixel($resource, $x, $y, $rgbNew);
          }  
        }  
      }
      
      imagedestroy($imgCanvas);
      imagedestroy($imgBlur);
    
    }
    
    return $image;
  }

}
