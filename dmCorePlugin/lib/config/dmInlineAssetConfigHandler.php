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

    if ($type === "js")
    {
      switch($package)
      {
        case 'core':
          $path = '/'.sfConfig::get('dm_core_asset').'/js/'.$name; break;
        case 'lib':
          $path = '/'.sfConfig::get('dm_core_asset').'/lib/'.$name; break;
        case 'front':
          $path = '/'.sfConfig::get('dm_front_asset').'/js/'.$name; break;
        case 'admin':
          $path = '/'.sfConfig::get('dm_admin_asset').'/js/'.$name; break;
        default:
          throw new dmException('Error parsing assets : '.$package.' is not a valid package');
      }
    }
    elseif ($type === "css")
    {
      switch($package)
      {
        case 'core':
          $path = '/'.sfConfig::get('dm_core_asset').'/css/'.$name; break;
        case 'lib':
          $path = '/'.sfConfig::get('dm_core_asset').'/lib/'.$name; break;
        case 'front':
          $path = '/'.sfConfig::get('dm_front_asset').'/css/'.$name; break;
        case 'admin':
          $path = '/'.sfConfig::get('dm_admin_asset').'/css/'.$name; break;
        default:
          throw new dmException('Error parsing assets : '.$package.' is not a valid package');
      }
    }
    else
    {
      throw new dmException("$type is not a valid asset type");
    }

    if(!isset($path))
    {
      throw new dmException("Can not find path for asset $type.$package.$asset");
    }

    return $path;
  }
}