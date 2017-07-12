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
 * sfImageTransformImagickAdapter class.
 *
 * ImageMagick support for sfImageTransform.
 *
 *
 * @package sfImageTransform
 * @author Stuart Lowes <stuart.lowes@gmail.com>
 * @version SVN: $Id$
 */
class sfImageTransformImageMagickAdapter extends sfImageTransformAdapterAbstract
{
  /**
   * The image resource.
   * @access protected
   * @var resource
   *
   * @throws sfImageTransformException
  */
  protected $holder;

  /*
   * Supported MIME types for the sfImageImageMagickAdapter
   * and their associated file extensions
   * @var array
   */
  protected $types = array(
    'image/jpeg' => array('jpeg','jpg'),
    'image/gif' => array('gif'),
    'image/png' => array('png')
  );

  /**
   * Initialize the object. Check for imagick extension. An exception is thrown if not installed
   *
   * @throws sfImageTransformException
   */
  public function __construct()
  {
    // Check that the GD extension is installed and configured
    if (!extension_loaded('imagick'))
    {
      throw new sfImageTransformException('The image processing library ImageMagick is not enabled. See PHP Manual for installation instructions.');
    }

    $this->setHolder(new Imagick());
  }

  /**
   * Tidy up the object
  */
  public function __destruct()
  {
    if ($this->hasHolder())
    {
      $this->getHolder()->destroy();
    }
  }

  /**
   * Create a new empty (1 x 1 px) gd true colour image
   *
   * @param integer Image width
   * @param integer Image Height
   */
  public function create($x=1, $y=1)
  {
    $image = new Imagick();
    $image->newImage($x, $y, new ImagickPixel('white'));
    $image->setImageFormat('png');
    $this->setHolder($image);
  }

  /**
   * Load and sets the resource from a existing file
   *
   * @param string
   * @return boolean
   *
   * @throws sfImageTransformException
   */
  public function load($filename, $mime)
  {
    if (preg_match('/image\/.+/',$mime))
    {
      $this->holder = new Imagick($filename);
      $this->mime_type = $mime;
      $this->setFilename($filename);

      return true;
    }

    throw new sfImageTransformException(sprintf('Cannot load file %s as %s is an unsupported file type.', $filename, $mime));
  }

  /**
   * Load and sets the resource from a string
   *
   * @param string
   * @return boolean
   *
   * @throws sfImageTransformException
   */
  
   public function loadString($string)
	{
    return $this->getHolder()->readImageBlob($string);;
	 }
 
  /**
   * Get the image as string
   *
   * @return string
   */
  public function __toString()
  {
    $this->getHolder()->setImageCompressionQuality($this->getQuality());

    return (string)$this->getHolder();
  }

  /**
   * Save the image to disk
   *
   * @return boolean
   */
  public function save()
  {
    $this->getHolder()->setImageCompressionQuality($this->getQuality());

    return $this->getHolder()->writeImage($this->getFilename());
  }

  /**
   * Save the image to the specified file
   *
   * @param string Filename
   * @param string MIME type
   * @return boolean
   */
  public function saveAs($filename, $mime='')
  {
    if ('' !== $mime)
    {
      $this->setMimeType($mime);
    }

    $this->getHolder()->setImageCompressionQuality($this->getQuality());

    return $this->getHolder()->writeImage($filename);
  }

  /**
   * Returns a copy of the adapter object
   *
   * @return sfImage
   */
  public function copy()
  {
    $copyObj = clone $this;

    $copyObj->setHolder($this->getHolder()->clone());

    return $copyObj;
  }

  /**
   * Gets the pixel width of the image
   *
   * @return integer
   */
  public function getWidth()
  {
    if ($this->hasHolder())
    {
      return $this->getHolder()->getImageWidth();
    }

    return 0;
  }

  /**
   * Gets the pixel height of the image
   *
   * @return integer
   */
  public function getHeight()
  {
    if ($this->hasHolder())
    {
      return $this->getHolder()->getImageHeight();
    }

    return 0;
  }

  /**
   * Sets the image resource holder
   * @param Imagick resource object
   * @return boolean
   *
   */
  public function setHolder($holder)
  {
    if (is_object($holder) && 'Imagick' === get_class($holder))
    {
      $this->holder = $holder;
      return true;
    }

    return false;
  }

  /**
   * Returns the image resource
   * @return resource
   *
   */
  public function getHolder()
  {
    if ($this->hasHolder())
    {
      return $this->holder;
    }

    return false;
  }

  /**
   * Returns whether there is a valid GD image resource
   * @return boolean
   *
   */
  public function hasHolder()
  {
    if (is_object($this->holder) && 'Imagick' === get_class($this->holder))
    {
      return true;
    }

    return false;
  }

  /**
   * Returns the supported MIME types
   * @return array
   *
   */
  public function getMimeType()
  {
    return $this->mime_type;
  }

 /**
   * Returns image MIME type
   * @param string valid MIME Type
   * @return boolean
   *
   */
  public function setMimeType($mime)
  {
    $this->mime_type = $mime;
    if ($this->hasHolder() && isset($this->types[$mime]))
    {
      $this->getHolder()->setImageFormat($this->types[$mime][0]);

      return true;
    }

    return false;
  }

  /**
   * Returns the name of the adapter
   * @return string
   *
   */
  public function getAdapterName()
  {
    return 'ImageMagick';
  }

  /**
   * Sets the image filename
   * @param integer Quality of the image
   *
   * @return boolean
   */
  public function setQuality($quality)
  {
    if (parent::setQuality($quality))
    {
      $this->getHolder()->setImageCompressionQuality($quality);

      return true;
    }

    return false;
  }
}
