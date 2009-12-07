<?php
/*
 * This file is part of the sfImageTransform package.
 * (c) 2009 Stuart Lowes <stuart.lowes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * sfImageResizeGeneric class
 *
 * generic resize transform
 *
 * @package sfImageTransform
 * @subpackage transforms
 * @author Stuart Lowes <stuart.lowes@gmail.com>
 * @author Miloslav Kmet <miloslav.kmet@gmail.com>
 * @author Victor Berchet <vberchet-sf@yahoo.com>
 * @version SVN: $Id$
 */
class sfImageResizeGeneric extends sfImageTransformAbstract
{
  /**
   * width of the target
   */
  protected $width = 0;

  /**
   * height of the target
   */
  protected $height = 0;

  /**
   * do we want to inflate the source image ?
   */
  protected $inflate = true;

  /**
   * do we want to keep the aspect ratio of the source image ?
   */
  protected $proportinal = false;

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
  public function __construct($width, $height, $inflate = true, $proportional = false)
  {
    $this->setWidth($width);
    $this->setHeight($height);
    $this->setInflate($inflate);
    $this->setProportional($proportional);
  }

  /**
   * sets the height of the thumbnail
   * @param integer $height of the image
   *
   * @return void
   */
  public function setHeight($height)
  {
    if(is_numeric($height) && $height > 0)
    {
      $this->height = (int)$height;
      
      return true;
    }
    
    return false;
  }

  /**
   * returns the height of the thumbnail
   *
   * @return integer
   */
  public function getHeight()
  {
    return $this->height;
  }

  /**
   * sets the width of the thumbnail
   * @param integer $width of the image
   *
   * @return void
   */
  public function setWidth($width)
  {
    if(is_numeric($width) && $width > 0)
    {
      $this->width = (int)$width;
      
      return false;
    }
  }

  /**
   * returns the width of the thumbnail
   *
   * @return integer
   */
  public function getWidth()
  {
    return $this->width;
  }

  /**
   * Choose if inflate is enabled or not
   * @param boolean
   *
   * @return boolean true if the parameter is valid
   */
  public function setInflate($inflate)
  {
    if($inflate === true || $inflate === false)
    {
      $this->inflate = $inflate;
      
      return true;
    }
    
    return false;
  }

  /**
   * returns the state of inflate
   *
   * @return boolean
   */
  public function getInflate()
  {
    return $this->inflate;
  }

  /**
   * Choose if the aspect ratio should be preserved
   * @param boolean
   *
   * @return boolean true if the parameter is valid
   */
  public function setProportional($proportional)
  {
    if($proportional === true || $proportional === false)
    {
      $this->proportional = $proportional;
      
      return true;
    }
    
    return false;
  }

  /**
   * returns the state of aspect ratio
   *
   * @return boolean
   */
  public function getProportional()
  {
    return $this->proportional;
  }

  /**
   * Apply the transformation to the image and returns the resized image
   */
  protected function transform(sfImage $image)
  {
    $source_w = $image->getWidth();
    $source_h = $image->getHeight();
    $target_w = $this->width;
    $target_h = $this->height;

    if (is_numeric($this->width) && $this->width > 0 && $source_w > 0)
    {
      if (!$this->inflate && $target_w > $source_w)
      {
        $target_w = $source_w;
      }
      
      if ($this->proportional)
      {
        // Compute the new height in order to keep the aspect ratio
        // and clamp it to the maximum height
        $target_h = round(($source_h / $source_w) * $target_w);
        
        if (is_numeric($this->height) && $this->height < $target_h && $source_h > 0)
        {
          $target_h = $this->height;
          $target_w = round(($source_w / $source_h) * $target_h);
        }
      }
    }

    if (is_numeric($this->height) && $this->height > 0 && $source_h > 0)
    {
      if (!$this->inflate && $target_h > $source_h)
      {
        $target_h = $source_h;
      }
      
      if ($this->proportional)
      {
        // Compute the new width in order to keep the aspect ratio
        // and clamp it to the maximum width
        $target_w = round(($source_w / $source_h) * $target_h);
        
        if (is_numeric($this->width) && $this->width < $target_w && $source_w > 0)
        {
          $target_w = $this->width;
          $target_h = round(($source_h / $source_w) * $target_w);
        }
      }
    }
    
    return $image->resizeSimple($target_w, $target_h);
  }
}
