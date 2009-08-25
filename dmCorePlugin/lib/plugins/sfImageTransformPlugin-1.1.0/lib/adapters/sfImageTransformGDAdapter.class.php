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
 * sfImageTransformGDAdapter class.
 *
 * GD support for sfImageTransform.
 *
 * @package sfImageTransform
 * @subpackage adapters
 * @author Stuart Lowes <stuart.lowes@gmail.com>
 * @version SVN: $Id$
 */
class sfImageTransformGDAdapter extends sfImageTransformAdapterAbstract
{
  /**
   * The image resource.
   * @access protected
   * @var resource
  */
  protected $holder;

  /*
   * Supported MIME types for the sfImageGDAdapter
   * and their associated file extensions
   * @var array
   */
  protected $types = array(
    'image/jpeg' => array('jpeg','jpg'),
    'image/gif' => array('gif'),
    'image/png' => array('png')
  );

  /*
   * List of GD functions used to load specific image types
   * @var array
   */
  protected $loaders = array(
    'image/jpeg' => 'imagecreatefromjpeg',
    'image/jpg' => 'imagecreatefromjpeg',
    'image/gif' => 'imagecreatefromgif',
    'image/png' => 'imagecreatefrompng'
  );

  /*
   * List of GD functions used to create specific image types
   * @var array
   */
  protected $creators = array(
    'image/jpeg' => 'imagejpeg',
    'image/jpg' => 'imagejpeg',
    'image/gif' => 'imagegif',
    'image/png' => 'imagepng'
  );

  /**
   * Initialize the object. Check for GD extension. An exception is thrown if not installed
   *
   * @throws sfImageTransformException
   */
  public function __construct()
  {
    // Check that the GD extension is installed and configured
    if (!extension_loaded('gd'))
    {
      throw new sfImageTransformException('The image processing library GD is not enabled. See PHP Manual for installation instructions.');
    }
  }

  /**
   * Tidy up the image resources
   */
  public function __destruct()
  {
    if ($this->hasHolder())
    {
      imagedestroy($this->getHolder());
    }
  }

  /**
   * Create a new empty (1 x 1 px) gd true colour image
   * @param integer Width
   * @param integer Height
   */
  public function create($x=1, $y=1)
  {
    $this->setHolder(imagecreatetruecolor($x,$y));
    imagefill($this->holder,0,0,imagecolorallocate($this->getHolder(), 255, 255, 255));
  }

  /**
   * Load and sets the resource from a existing file
   * @param string
   * @return boolean
   *
   * @throws sfImageTransformException
   */
  public function load($filename, $mime)
  {
    if (array_key_exists($mime,$this->loaders))
    {
      $this->holder = $this->loaders[$mime]($filename);
      $this->mime_type = $mime;
      $this->setFilename($filename);

      return true;
    }

    else
    {
        throw new sfImageTransformException(sprintf('Cannot load file %s as %s is an unsupported file type.', $filename, $mime));
    }
  }

  /**
   * Loads an image from a string
   * @param string String image
   * @return boolean
   */
  public function loadString($string)
  {
    $resource = imagecreatefromstring($string);

    if (is_resource($resource) && 'gd' === get_resource_type($resource))
    {
      $this->setHolder($resource);

      return true;
    }

    return false;
  }

  /**
   * Get the image as string
   *
   * @return string
   */
  public function __toString()
  {
    ob_start();
    $this->__output(false);

    return ob_get_clean();
  }

  /**
   * Save the image to disk
   *
   * @return boolean
   */
  public function save()
  {
    $this->__output(true);
    
    return true; 
  }

  /**
   * Save the image to disk
   *
   * @param string Filename
   * @param string MIME type
   * @return boolean
   */
  public function saveAs($filename, $mime='')
  {
    if ('' !== $mime)
    {
      if (!$this->setMimeType($mime))
      {
        throw new sfImageTransformException(sprintf('Cannot convert as %s is an unsupported type' ,$mime));
      }
    }

    $this->setFilename($filename);

    return $this->__output(true, $filename);
  }

  /**
   * Returns a copy of the adapter object
   *
   * @return sfImage
   */
  public function copy()
  {
    $copyObj = clone $this;

    $copy = $this->getTransparentImage($this->getWidth(), $this->getHeight());
    imagecopy($copy, $this->getHolder(), 0, 0, 0, 0, $this->getWidth(), $this->getHeight());

    $copyObj->setHolder($copy);

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
      return imagesx($this->getHolder());
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
      return imagesy($this->getHolder());
    }

    return 0;
  }

  /**
   * Sets the image resource holder
   * @param GD resource object
   * @return boolean
   *
   */
  public function setHolder($resource)
  {

    if (is_resource($resource) && 'gd' === get_resource_type($resource))
    {

      $this->holder = $resource;
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
    if (is_resource($this->holder) && 'gd' === get_resource_type($this->holder))
    {
      return true;
    }

    return false;
  }

 /**
   * Returns image MIME type
   * @return boolean
   *
   */
  public function getMIMEType()
  {
    return $this->mime_type;
  }

 /**
   * Returns image MIME type
   * @param string valid MIME Type
   * @return boolean
   *
   */
  public function setMIMEType($mime)
  {
    if (array_key_exists($mime,$this->loaders))
    {
      $this->mime_type = $mime;
      return true;
    }

    return false;
  }

 /**
   * Returns image MIME type
   * @return boolean
   *
   */
  public function getMIMETypeFromFilename($filename)
  {

    $path = pathinfo($filename);

    foreach($this->types as $type => $extensions)
    {
      if (in_array($path['extension'], $extensions))
      {
        return $type;
      }

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
    return 'GD';
  }

  /**
   * Returns the image color for a hex value (format #XXXXXX).
   *
   * @param resource image resource
   * @param string full hex value of the color or GD constant
   * @return integer
   */
  public function getColorByHex($image, $color)
  {

    if (preg_match('/#[\d\w]{6}/',$color))
    {
      $rgb = sscanf($color, '#%2x%2x%2x');
      $color = imagecolorallocate($image, $rgb[0], $rgb[1], $rgb[2]);

      return $color;
    }

    return $color;
  }

 /**
   * Returns image in current format and optionally writes image to disk
   * @return resource
   *
   * @throws sfImageTransformException
   */
  protected function __output($to_file=false, $filename='')
  {
    $file = null;

    // Are we saving to file, if so get the filename to save to
    if ($to_file)
    {
      $file = $filename;
      if ('' === $file)
      {
        $file = $this->getFilename();
      }
    }

    $mime = $this->getMimeType();

    if (array_key_exists($mime,$this->creators))
    {

      switch ($mime)
      {

        case 'image/jpeg':
        case 'image/jpg':
          if (is_null($this->quality))
          {
            $this->quality = 75;
          }
          $output = $this->creators[$mime]($this->holder,$file,$this->getImageSpecificQuality($this->quality, $mime));
          break;

        case 'image/png':
          imagesavealpha($this->holder, true);
          $output = $this->creators[$mime]($this->holder,$file,$this->getImageSpecificQuality($this->quality, $mime), null);
          break;

        case 'image/gif':

          if (!is_null($file))
          {
            $output = $this->creators[$mime]($this->holder,$file);
          }
          else
          {
            $output = $this->creators[$mime]($this->holder);
          }
          break;

        default:
          throw new sfImageTransformException(sprintf('Cannot convert as %s is an unsupported type' ,$mime));
      }
    }
    else
    {
      throw new sfImageTransformException(sprintf('Cannot convert as %s is an unsupported type' ,$mime));
    }

    return $output;
  }

  protected function getImageSpecificQuality($quality, $mime)
  {
    // Range is from 0-100

    if ('image/png' === $mime)
    {

      return 9 - round($quality * (9/100));
    }

    return $quality;
  }

 /**
   * Helper method. Returns a transparent image resource of the specified size
   * @param integer width
   * @param integer height
   * @return resource image
   *
   * @throws sfImageTransformException
   */
  public function getTransparentImage($w, $h)
  {

    $resource = $this->getHolder();

    $dest_resource = imagecreatetruecolor((int)$w, (int)$h);

    // Preserve alpha transparency
    if (in_array($this->getMIMEType(), array('image/gif','image/png')))
    {
      $index = imagecolortransparent($resource);

      // Handle transparency
      if ($index >= 0)
      {

        // Grab the current images transparent color
        $index_color = imagecolorsforindex($resource, $index);

        // Set the transparent color for the resized version of the image
        $index = imagecolorallocate($dest_resource, $index_color['red'], $index_color['green'], $index_color['blue']);

        // Fill the entire image with our transparent color
        imagefill($dest_resource, 0, 0, $index);

        // Set the filled background color to be transparent
        imagecolortransparent($dest_resource, $index);

      }

      // Always make a transparent background color for PNGs that don't have one allocated already
      elseif ($this->getMIMEType() == 'image/png')
      {

        // Disabled blending
        imagealphablending($dest_resource, false);

        // Grab our alpha tranparency color
        $color = imagecolorallocatealpha($dest_resource, 0, 0, 0, 127);

        // Fill the entire image with our transparent color
        imagefill($dest_resource, 0, 0, $color);

        // Re-enable transparency blending
        imagesavealpha($dest_resource, true);
      }
    }

    return $dest_resource;

  }

}
