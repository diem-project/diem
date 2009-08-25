<?php
/*
 * This file is part of the sfImageTransform package.
 * (c) 2007 Stuart <stuart.lowes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 *
 * sfImageOverlaysGD class.
 *
 * Overlays GD image on top of another GD image.
 *
 * Overlays an image at a set point on the image.
 *
 * @package sfImageTransform
 * @subpackage transforms
 * @author Stuart Lowes <stuart.lowes@gmail.com>
 * @version SVN: $Id$
 */
class sfImageOverlayImageMagick extends sfImageTransformAbstract
{
  /**
   * The composite operator
   */
  protected $compose = IMagick::COMPOSITE_DEFAULT;

  /**
   * The overlay sfImage.
   */
  protected $overlay;

  /**
   * The opacity applied to the overlay.
   */
  protected $opacity = null;

  /**
   * The left coordinate for the overlay position.
   */
  protected $left = 0;

  /**
   * The top coordinate for the overlay position.
   */
  protected $top = 0;

  /**
   * The named position of for the overlay
   */
  protected $position = null;

  /**
   * available labels for overlay positions 
   */
  protected $labels = array(
                            'top', 'bottom','left' ,'right', 'middle', 'center',
                            'top-left', 'top-right', 'top-center',
                            'middle-left', 'middle-right', 'middle-center',
                            'bottom-left', 'bottom-right', 'bottom-center',
                           );
                           
  /**
   * Construct an sfImageOverlay object.
   *
   * @param sfImage $overlay  - the image for the overlay
   * @param mixed   $position - the named position as string, or the array of exact coordinates
   * @param float   $opacity  - the opacity for the overlay image
   * @param integer $compose  - the composite operator
   *
   * @return void
   */
  public function __construct(sfImage $overlay,  $position='top-left', $opacity=null, $compose=IMagick::COMPOSITE_DEFAULT)
  {
    $this->setOverlay($overlay);
    $this->setOpacity($opacity);
    $this->setCompose($compose);

    if (is_array($position) && count($position)==2)
    {
      $this->setLeft($position[0]);
      $this->setTop($position[1]);
    }

    else
    {
      $this->setPosition($position);
    }
  }

  /**
   * sets the over image.
   *
   * @param sfImage
   */
  function setOverlay(sfImage $overlay)
  {
    $this->overlay = $overlay;

  }
  /**
   * returns the overlay sfImage object.
   *
   * @return sfImage
   */
  function getOverlay()
  {
    return $this->overlay;
  }

  /**
   * Sets the left coordinate
   *
   * @param integer
   */
  public function setLeft($left)
  {
    $this->left = $left;
  }

  /**
   * returns the left coordinate.
   *
   * @return integer
   */
  public function getLeft()
  {
    return $this->left;
  }

  /**
   * set the top coordinate.
   *
   * @param integer
   */
  public function setTop($top)
  {
    $this->top = $top;
  }

  /**
   * returns the top coordinate.
   *
   * @return integer
   */
  public function getTop()
  {
    return $this->top;
  }

  /**
   * Set named position
   *
   * @param string $position named position. Possible named positions:
   *                - top (alias of top-center), 
   *                - bottom (alias of botom-center), 
   *                - left ( alias of top-left), 
   *                - right (alias of top-right), 
   *                - center (alias of middle-center1), 
   *                - top-left, top-right, top-center, 
   *                - middle-left, middle-right, middle-center,
   *                - bottom-left, bottom-right, bottom-center
   *
   * @return void
   */
  public function setPosition($position)
  {
  
    // Backwards compatibility
    $map = array(
                  'left' => 'west', 'right' => 'east', 'top' => 'north', 'bottom' => 'south', 
                  'top west' => 'top-left', 'top east' => 'top-right', 'south west' => 'bottom-left', 'south east' => 'bottom-right'
                );
                
    if($key = array_search($position, $map))
    {
      $message = sprintf('sfImageTransformPlugin overlay position \'%s\' is deprecated use \'%s\' instead', $position, $key);
      sfContext::getInstance()->getEventDispatcher()->notify(new sfEvent($this, 'application.log', array($message, 'priority' => sfLogger::ERR)));
      $this->position = $map[$key];
    }
  
    if(in_array($position, $this->labels))
    {
      $this->position = strtolower($position);
      
      return true;
    }
    
    return false;
  }

  /**
   * returns the position name
   *
   * @return string
   */
  public function getPosition()
  {
    return $this->position;
  }

  /**
   * Computes the offset of the overlayed image
   * and sets the top and left coordinates based on the named position
   *
   * @param sfImage $image canvas image
   *
   * @return void
   */
  protected function computeCoordinates(sfImage $image)
  {
    $position = $this->getPosition();

    // no named position nothing to compute
    if (is_null($position))
    {
      return;
    }

    $resource   = $image->getAdapter()->getHolder();
    $resource_x = $resource->getImageWidth();
    $resource_y = $resource->getImageHeight();

    $overlay    = $this->getOverlay()->getAdapter()->getHolder();
    $overlay_x  = $overlay->getImageWidth();
    $overlay_y  = $overlay->getImageHeight();

    switch ($position)
    {
      case 'top':
      case 'top-left':
        $this->setLeft(0);
        $this->setTop(0);
        break;
      case 'bottom':
      case 'bottom-left':
        $this->setLeft(0);
        $this->setTop($resource_y-$overlay_y);
        break;
      case 'left':
        $this->setLeft(0);
        $this->setTop(round(($resource_y - $overlay_y)/2));
        break;
      case 'right':
        $this->setLeft(round($resource_x - $overlay_x));
        $this->setTop(round(($resource_y - $overlay_y)/2));
        break;
      case 'top-right':
        $this->setLeft($resource_x - $overlay_x);
        $this->setTop(0);
        break;
      case 'bottom-right':
        $this->setLeft($resource_x - $overlay_x);
        $this->setTop($resource_y - $overlay_y);
        break;
      case 'bottom-center':
        $this->setLeft(round(($resource_x - $overlay_x) / 2));
        $this->setTop(round($resource_y - $overlay_y));
        break;
      case 'center':
      case 'middle-center':
        $this->setLeft(round(($resource_x - $overlay_x) / 2));
        $this->setTop(round(($resource_y - $overlay_y) / 2));
        break;
      case 'middle-left':
        $this->setLeft(0);
        $this->setTop(round(($resource_y - $overlay_y) / 2));
        break;
      case 'middle-right':
        $this->setLeft(round($resource_x - $overlay_x));
        $this->setTop(round(($resource_y - $overlay_y) / 2));
        break;
      case 'bottom-left':
      default:
        $this->setLeft(0);
        $this->setTop($resource_y - $overlay_y);
        break;
    }
  }

  /**
   * sets the opacity used for the overlay.
   *
   * @param integer
   */
  function setOpacity($opacity)
  {
    if(is_numeric($opacity) && $opacity > 1)
    {
      $this->opacity = $opacity / 100;
    }

    else if (is_float($opacity))
    {
      $this->opacity = abs($opacity);
    }

    else
    {
      $this->opacity = $opacity;
    }
  }

  /**
   * returns the opacity used for the overlay.
   *
   * @return mixed
   */
  function getOpacity()
  {
    return $this->opacity;
  }

  /**
   * Sets the composite operator
   *
   * @param integer valid IMagick composite opeator
   *
   * @return void
   * @see http://php.net/manual/en/imagick.constants.php#imagick.constants.compositeop
   */
  public function setCompose($compose=IMagick::COMPOSITE_DEFAULT)
  {
    $this->compose = $compose;
  }

  /**
   * return the composite operator
   *
   * @return integer composite operator
   */
  public function getCompose()
  {
    return $this->compose;
  }


  /**
   * Apply the transform to the sfImage object.
   *
   * @param sfImage
   *
   * @return sfImage
   */
  protected function transform(sfImage $image)
  {
    // compute the named coordinates
    $this->computeCoordinates($image);

    $resource = $image->getAdapter()->getHolder();
    $overlay = $this->getOverlay();

    if (!is_null($this->getOpacity()))
    {
      $overlay->getAdapter()->getHolder()->setImageOpacity($this->getOpacity());
    }

    $resource->compositeImage($overlay->getAdapter()->getHolder(), $this->getCompose(), $this->getLeft(), $this->getTop());

    $image->getAdapter()->setHolder($resource);

    return $image;
  }
}
