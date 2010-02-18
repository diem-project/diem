<?php

class dmProject
{

  protected static
  $key,
  $hash,
  $models,
  $allModels,
  $dmModels;

  /**
   * Returns project key based on his dir_name
   */
  public static function getKey()
  {
    if (null === self::$key)
    {
      self::$key = basename(sfConfig::get('sf_root_dir'));
    }
    
    return self::$key;
  }

  /**
   * Returns project key based on his root dir
   */
  public static function getHash()
  {
    if (null === self::$hash)
    {
      self::$hash = substr(md5(sfConfig::get('sf_root_dir')), -8);
    }
    
    return self::$hash;
  }

  public static function getModels()
  {
    if (null === self::$models)
    {
      $baseFiles = glob(dmOs::join(sfConfig::get('sf_lib_dir'), 'model/doctrine/base/Base*.class.php'));
      
      self::$models = self::getModelsFromBaseFiles($baseFiles);
    }

    return self::$models;
  }
  
  public static function getAllModels()
  {
    if (null === self::$allModels)
    {
      $baseFiles = sfFinder::type('file')
      ->name('/^Base\w+.class.php$/i')
      ->in(dmOs::join(sfConfig::get('sf_lib_dir'), 'model/doctrine'));

      self::$allModels = self::getModelsFromBaseFiles($baseFiles);
    }

    return self::$allModels;
  }
  
  public static function getDmModels()
  {
    if (null === self::$dmModels)
    {
      $baseFiles = sfFinder::type('file')
      ->name('/Base\w+.class.php/i')
      ->maxDepth(0)
      ->in(dmOs::join(sfConfig::get("sf_lib_dir"), "model/doctrine/dmCorePlugin/base"));

      self::$dmModels = self::getModelsFromBaseFiles($baseFiles);
    }
    
    return self::$dmModels;
  }

  protected static function getModelsFromBaseFiles(array $files)
  {
    $models = array();
    foreach($files as $file)
    {
      $models[] = preg_replace('|^Base(\w+).class.php$|', '$1', basename($file));
    }

    return $models;
  }
  
  public static function getRootDir()
  {
    return dmOs::normalize(sfConfig::get('sf_root_dir'));
  }
  
  public static function getNormalizedRootDir()
  {
    return dmOs::normalize(self::getRootDir());
  }
  
  /**
   * remove sfConfig::get('sf_root_dir') from path
   */
  public static function unRootify($path)
  {
    if (self::isInProject($path))
    {
      $path = substr($path, strlen(self::getRootDir()));
    }
    
    return trim($path, '/');
  }
  
  /**
   * add sfConfig::get('sf_root_dir') to path
   */
  public static function rootify($path)
  {
    if (!self::isInProject($path))
    {
      $path = dmOs::join(self::getRootDir(), $path);
    }
    else
    {
      $path = dmOs::join($path);
    }
    
    return $path;
  }
  
  public static function isInProject($path)
  {
    return strpos(dmOs::normalize($path), self::getRootDir()) === 0;
  }
  
  public static function appExists($application)
  {
    return file_exists(self::rootify('apps/'.$application.'/config/'.$application.'Configuration.class.php'));
  }
}