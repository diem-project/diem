<?php

class dmMediaTools
{

	public static function getAdminUrlFor($object)
	{
	  if($object instanceof DmMediaFolder)
	  {
		  if (!$baseUrl = sfConfig::get('dm_mediaLibrary_folderBaseUrl'))
		  {
		    $baseUrl = sfContext::getInstance()->getController()->genUrl('@dm_media_library_path?path=__PATH__');
		    sfConfig::set('dm_mediaLibrary_folderBaseUrl', $baseUrl);
		  }
	    return str_replace('__PATH__', $object->getRelPath(), $baseUrl);
	  }
    elseif($object instanceof DmMedia)
    {
      return 'dmMediaLibrary/file?media_id='.$object->getId();
    }
	  else
	  {
	  	throw new dmException('Can not generate url for '.$object);
	  }
	}


  /**
   * @return string
   */
  public static function dot2slash($txt)
  {
    return preg_replace('#[\+\s]+#', '/', $txt);
  }

  public static function getType($filepath)
  {
    $suffix = substr($filepath, strrpos($filepath, '.') - strlen($filepath) + 1);
    if (self::isImage($suffix))
    {
      return 'image';
    }
    else if (self::isText($suffix))
    {
      return 'txt';
    }
    else if (self::isArchive($suffix))
    {
      return 'archive';
    }
    else
    {
      return $suffix;
    }
  }

  public static function isImage($ext)
  {
    return in_array(strtolower($ext), array('png', 'jpg', 'jpeg', 'gif'));
  }

  public static function isText($ext)
  {
    return in_array(strtolower($ext), array('txt', 'php', 'markdown'));
  }

  public static function isArchive($ext)
  {
    return in_array(strtolower($ext), array('zip', 'gz', 'tgz', 'rar', '7z'));
  }

  public static function getInfo($dir, $filename)
  {
    $info = array();
    $info['ext']  = substr($filename, strpos($filename, '.') - strlen($filename) + 1);
    $stats = stat($dir.'/'.$filename);
    $info['size'] = $stats['size'];
    $info['thumbnail'] = true;
    if (self::isImage($info['ext']))
    {
      if (is_readable($dir.'/thumbnail/small_'.$filename))
      {
        $info['icon'] = $dir.'/thumbnail/small_'.$filename;
      }
      else
      {
        $info['icon'] = $dir.'/'.$filename;
        $info['thumbnail'] = false;
      }
    }
    else
    {
      if (is_readable(sfConfig::get('sf_web_dir').'/dmPlugin/images/media/'.$info['ext'].'.png'))
      {
        $info['icon'] = '/dmPlugin/images/media/'.$info['ext'].'.png';
      }
      else
      {
        $info['icon'] = '/dmPlugin/images/media/unknown.png';
      }
    }

    return $info;
  }

  public static function sanitizeDirName($file)
  {
    return trim(
      preg_replace('/[^\w\._-]+/i', '-', dmString::removeAccents($file)),
      '-'
    );
  }

  public static function sanitizeFileName($file)
  {
    return trim(
      preg_replace("|[".preg_quote('\'*"/\[]:;|=,', '|')."]+|i", '-', $file)
    );
  }

  public static function mkdir($dirName, $parentDirName)
  {
    $dirName = rtrim($dirName, '/');

    if (!is_dir(self::getMediaDir(true) . $parentDirName))
    {
      list($parent, $name) = self::splitPath($parentDirName);
      if ($parent && $name)
      {
        $result = self::mkdir($name, $parent);
        if (!$result)
        {
          return false;
        }
      }
    }

    if (!$dirName)
    {
    	return true;
      throw new sfException('Trying to make a folder with no name');
    }
    $parentDirName = ($parentDirName)? rtrim($parentDirName, '/') . '/' : '';
    $absCurrentDir = sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR .$parentDirName.$dirName;
    $mkdir_success = true;

    try
    {
      if (!is_dir($absCurrentDir))
      {
        mkdir($absCurrentDir, 0777);
        chmod($absCurrentDir, 0777);
      }
    }
    catch(sfException $e)
    {
      $mkdir_success = false;
    }

    return $mkdir_success && is_dir($absCurrentDir);
  }

  public static function deleteTree($root)
  {
    if (!is_dir($root))
    {
      return false;
    }
    foreach(glob($root.'/*', GLOB_ONLYDIR) as $dir)
    {
      if (!is_link($dir))
      {
        self::deleteTree($dir);
      }
    }

    return rmdir($root);
  }

  public static function createAssetUrl($path, $filename, $thumbnail_type = 'full', $file_system = true)
  {
    if ($thumbnail_type == 'full')
    {
      return self::getMediaDir($file_system) . $path . DIRECTORY_SEPARATOR . $filename;
    }
    else
    {
      return self::getMediaDir($file_system) . self::getThumbnailDir($path) . $thumbnail_type . '_' . $filename;
    }
  }

  public function getAssetImageTag($sf_media, $thumbnail_type = 'full', $file_system = false, $options = array())
  {
    $options = array_merge($options, array(
      'alt'   => $sf_media->getCopyright(),
      'title' => $sf_media->getCopyright()
    ));

    return image_tag(self::getAssetUrl($sf_media, $thumbnail_type, $file_system), $options);
  }

  /**
   * Retrieves a sfMedia object from a relative URL like
   *    /medias/foo/bar.jpg
   * i.e. the kind of URL returned by getAssetUrl($sf_media, 'full', false)
   */
  public static function getAssetFromUrl($url)
  {
    $url = str_replace(aze::getUploadDirName(), '', $url);
    $parts = explode('/', $url);
    $filename = array_pop($parts);
    $relPath = '/' . implode('/', $parts);

    $c = new Criteria();
    $c->add(sfMediaPeer::FILENAME, $filename);
    $c->add(sfMediaPeer::REL_PATH, $relPath ?  $relPath : null);

    return sfMediaPeer::doSelectOne($c);
  }


  public static function getParent($path)
  {
    $dirs = explode('/', $path);
    array_pop($dirs);

    return join('/', $dirs);
  }

  /**
   * Splits a path into a basepath and a name
   *
   * @param string $path
   * @return array $relative_path $name
   */
  public static function splitPath($path, $separator = DIRECTORY_SEPARATOR)
  {
    $path = rtrim($path, $separator);
    $dirs = preg_split('/' . preg_quote($separator, '/') . '+/', $path);
    $name = array_pop($dirs);
    $relative_path =  implode($separator, $dirs);

    return array($relative_path, $name);
  }

  public static function log($message, $color = '')
  {
    echo $message;return;
    switch($color)
    {
      case 'green':
        $message = "\033[32m".$message."\033[0m\n";
        break;
      case 'red':
        $message = "\033[31m".$message."\033[0m\n";
        break;
      case 'yellow':
        $message = "\033[33m".$message."\033[0m\n";
        break;
      default:
        $message = $message . "\n";
    }
    fwrite(STDOUT, $message);
  }

}