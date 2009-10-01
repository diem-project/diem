<?php
/*
 * This file is part of the sfImageTransform package.
 * (c) 2007 Stuart Lowes <stuart.lowes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * sfImageThumbnailGeneric class
 *
 * generic thumbnail transform
 *
 * Create a thumbnail 100 x 100, with the image resized to fit
 * <code>
 * <?php
 * $img = new sfImage('image1.jpg');
 * $img->thumbnail(100, 100);
 * $img->setQuality(50);
 * $img->saveAs('thumbnail.png');
 * ?>
 * </code>
 *
 * @package sfImageTransform
 * @subpackage transforms
 * @author Stuart Lowes <stuart.lowes@gmail.com>
 * @author Miloslav Kmet <miloslav.kmet@gmail.com>
 * @version SVN: $Id$
 */
class sfImageThumbnailGeneric extends sfImageTransformAbstract
{
  /**
   * width of the thumbnail
   */
  protected $width;

  /**
   * height of the thumbnail
   */
  protected $height;

  /**
   * method to be used for thumbnail creation. default is scale.
   */
  protected $method = 'fit';
  
  /**
   * available methods for thumbnail creation
   */
  protected $methods = array('fit', 'scale', 'inflate','deflate', 'left' ,'right', 'top', 'bottom', 'center');
  
  /*
   * background color in hex or null for transparent
   */
  protected $background = null;

  /**
   * constructor
   *
   * @param integer $width of the thumbnail
   * @param integer $height of the thumbnail
   * @param string type of thumbnail method to be created
   *
   * @return void
   */
  public function __construct($width, $height, $method='fit', $background=null)
  {   
    if(!$this->setWidth($width) || !$this->setHeight($height))
		{
			throw new sfImageTransformException(sprintf('Cannot perform thumbnail, a valid width and height must be supplied'));
		}
    $this->setMethod($method);
    $this->setBackground($background);    
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
      
      return true;
    }
    
    return false;
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
   * returns the width of the thumbnail
   * @param string thumbnail method. Options are scale (default), deflate (or inflate), right, left, top, bottom, scale
   *
   * @return integer
   */
  public function setMethod($method)
  {
    // Backwards compatibility
    $map = array('left' => 'west', 'right' => 'east', 'top' => 'north', 'bottom' => 'south');
    if($key = array_search($method, $map))
    {
      $message = sprintf('sfImageTransformPlugin thumbnail method \'%s\' is depreciated use \'%s\'', $method, $key);
      sfContext::getInstance()->getEventDispatcher()->notify(new sfEvent($this, 'application.log', array($message, 'priority' => sfLogger::ERR)));
    }
  
    if(in_array($method, $this->methods))
    {
      $this->method = strtolower($method);
      
      return true;
    }
    
    return false;
  }

  /**
   * returns the method for thumbnail creation
   *
   * @return integer
   */
  public function getMethod()
  {
    return $this->method;
  }
  
  /**
   * Sets background color.
   *
   * @param string
   */
  public function setBackground($color)
  {
    $this->background = $color;
  }

  /**
   * Gets background color.
   *
   * @return string
   */
  public function getBackground()
  {
    return $this->background;
  }
  
  /**
   * Apply the transformation to the image and returns the image thumbnail
   */
  protected function transform(sfImage $image)
  {
    $resource_w = $image->getWidth();
    $resource_h = $image->getHeight();

    $scale_w    = $this->getWidth()/$resource_w;
    $scale_h    = $this->getHeight()/$resource_h;
    switch ($this->getMethod())
    {
      case 'deflate':
      case 'inflate':

        return $image->resize($this->getWidth(), $this->getHeight());

      case 'left':
        $image->scale(max($scale_w, $scale_h));

        return $image->crop(0, (int)round(($image->getHeight() - $this->getHeight()) / 2), $this->getWidth(), $this->getHeight());

      case 'right':
        $image->scale(max($scale_w, $scale_h));

        return $image->crop(($image->getWidth() - $this->getWidth()), (int)round(($image->getHeight() - $this->getHeight()) / 2),$this->getWidth(), $this->getHeight());

      case 'top':
        $image->scale(max($scale_w, $scale_h));

        return $image->crop((int)round(($image->getWidth() - $this->getWidth()) / 2), 0, $this->getWidth(), $this->getHeight());

      case 'bottom':
        $image->scale(max($scale_w, $scale_h));

        return $image->crop((int)round(($image->getWidth() - $this->getWidth()) / 2), ($image->getHeight() - $this->getHeight()), $this->getWidth(), $this->getHeight());
        
      case 'center':
        $image->scale(max($scale_w, $scale_h));
        
        $left = (int)round(($image->getWidth() - $this->getWidth()) / 2);
        $top  = (int)round(($image->getHeight() - $this->getHeight()) / 2);

        return $image->crop($left, $top, $this->getWidth(), $this->getHeight());
      case 'scale':
        return $image->scale(min($scale_w, $scale_h));

      case 'fit':           
      default:
        $img = clone $image;

        $image->create($this->getWidth(), $this->getHeight());
        
        // Set a background color if specified
        if(!is_null($this->getBackground()) && $this->getBackground() != '')
        {
          $image->fill(0,0, $this->getBackground());
        }

        $img->scale(min($this->getWidth() / $img->getWidth(), $this->getHeight() / $img->getHeight()));
        
        $image->overlay($img, 'center');

        return $image;
        
    }
  }
}
