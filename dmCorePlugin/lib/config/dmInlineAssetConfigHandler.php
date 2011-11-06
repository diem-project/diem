<?php

require_once(dirname(__FILE__)."/dmInlineConfigHandler.php");

class dmInlineAssetConfigHandler extends dmInlineConfigHandler
{

  /**
   * Gets values from the configuration array.
   *
   * @param string $prefix    The prefix name
   * @param string $category  The category name
   * @param mixed  $keys      The key/value array
   *
   * @return array The new key/value array
   */
  protected function getValues($prefix, $category, $keys)
  {
    $values = array();

    // loop through all key/value pairs
    foreach ((array)$keys as $key => $value)
    {
      if (0 === strncmp($value, 'http://', 7) || 0 === strncmp($value, 'https://', 8))
      {
        $values[$prefix.$this->separator.$category.$this->separator.$key] = $value;
      }
      else
      {
        $values[$prefix.$this->separator.$category.$this->separator.$key] = self::getPathFromWebDir($prefix, $category.'.'.$value);
      }
    }

    return $values;
  }

  public static function getPathFromWebDir($type, $asset)
  {
    $package = substr($asset, 0, strpos($asset, '.'));

    if (in_array($package, array('core', 'lib', 'front', 'admin')))
    {
      $name = substr($asset, strpos($asset, '.')+1);
    }
    else
    {
      $name = $asset;
    }

    switch($type)
    {
      case 'js':
        switch($package)
        {
          case 'core':
            $path = '/'.sfConfig::get('dm_core_asset').'/js/'.$name.'.js'; break;
          case 'lib':
            $path = '/'.sfConfig::get('dm_core_asset').'/lib/'.$name.'.js'; break;
          case 'front':
            $path = '/'.sfConfig::get('dm_front_asset').'/js/'.$name.'.js'; break;
          case 'admin':
            $path = '/'.sfConfig::get('dm_admin_asset').'/js/'.$name.'.js'; break;
          default:
            $path = '/'.dmString::str_replace_once('.', '/'.$type.'/', $asset).'.js';
        }
        break;
      case 'css':
        switch($package)
        {
          case 'core':
            $path = '/'.sfConfig::get('dm_core_asset').'/css/'.$name.'.css'; break;
          case 'lib':
            $path = '/'.sfConfig::get('dm_core_asset').'/lib/'.$name.'.css'; break;
          case 'front':
            $path = '/'.sfConfig::get('dm_front_asset').'/css/'.$name.'.css'; break;
          case 'admin':
            $path = '/'.sfConfig::get('dm_admin_asset').'/css/'.$name.'.css'; break;
          default:
            $path = '/'.dmString::str_replace_once('.', '/'.$type.'/', $asset).'.css';
        }
        break;
      default:
        $path = '/'.dmString::str_replace_once('.', '/', $asset);
    }

    if(!isset($path))
    {
      throw new dmException("Can not find path for asset $type.$package.$asset");
    }

    //check for source version of the file in `dev` environment
    if (sfConfig::get('sf_environment')=='dev') {
      $webPath = sfConfig::get('sf_web_dir');
      $fsPath = $webPath.$path;

      $fsSourceFilePath = null;
      $fsDirName = dirname($fsPath);
      $fsFilename = basename($fsPath);

      if (strpos($fsFilename, '.min.')) {
        $possibleFsSourceFilePath = $fsDirName.DIRECTORY_SEPARATOR.str_replace('.min.','.',$fsFilename);
        if (file_exists($possibleFsSourceFilePath)) {
          $fsSourceFilePath = $possibleFsSourceFilePath;
        }
      }
      if (is_null($fsSourceFilePath)) {
        $fsPathDirectories = explode(DIRECTORY_SEPARATOR, $fsDirName);
        $fsFilename = str_replace('.min.','.',$fsFilename);
        foreach ( $fsPathDirectories as $fsDirectory ) {
          if (in_array($fsDirectory, array('min','minified','compressed','minimized'))) {
            foreach (array('source','unminified','uncompressed','unminimized') as $possibleSourceDirName) {
              $search = DIRECTORY_SEPARATOR.$fsDirectory.DIRECTORY_SEPARATOR;
              $replace = DIRECTORY_SEPARATOR.$possibleSourceDirName.DIRECTORY_SEPARATOR;
              $possibleFsSourceFilePath = str_replace($search, $replace, $fsDirName.DIRECTORY_SEPARATOR).$fsFilename;
              if (file_exists($possibleFsSourceFilePath)) {
                $fsSourceFilePath = $possibleFsSourceFilePath;
                break;
              }
            }
            //break after a directory with minified files is found
            break;
          }
        }
        unset( $fsDirectory );
      }
      if (!is_null($fsSourceFilePath)) {
        $fs = dmContext::getInstance()->getFilesystem();
        $path = DIRECTORY_SEPARATOR.$fs->getRelativeDir($webPath.DIRECTORY_SEPARATOR, $fsSourceFilePath);
      }
    }



    return $path;
  }
}