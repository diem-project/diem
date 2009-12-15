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
    // loop through all key/value pairs
    foreach ($keys as $key => $value)
    {
      $values[$prefix.$this->separator.$category.$this->separator.$key] = self::getPathFromWebDir($prefix, $category.'.'.$value);
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
    
    return $path;
  }
}