<?php

class dmAsset
{
	const SEPARATOR = ".";

	protected static
	$config;

	public static function getConfig()
	{
    if (self::$config === null)
    {
      self::$config = include(sfContext::getInstance()->getConfigCache()->checkConfig('config/dm/assets.yml'));
    }
    return self::$config;
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
        //user asset
        default:
          $path = '/js/'.$name;
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
        //user asset
        default:
          $path = dm::getUser()->getTheme()->getPath('css/'.$name);
      }
    }
    else
    {
    	throw new dmException("$type n'est pas un type d'asset valide");
    }

    if(!isset($path))
    {
    	throw new dmException("Can not find path for asset $type.$package.$asset");
    }

    return $path;
	}

}