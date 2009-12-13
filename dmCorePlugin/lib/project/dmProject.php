<?php

class dmProject
{

  protected static
    $key,
    $models,
    $allModels,
    $dmModels;

  /*
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

  public static function getModels()
  {
    if (null === self::$models)
    {
      self::$models = array();
      foreach(glob(dmOs::join(sfConfig::get('sf_lib_dir'), 'model/doctrine/base/Base*.class.php')) as $baseModel)
      {
        self::$models[] = preg_replace('|^Base(\w+).class.php$|', '$1', basename($baseModel));
      }
    }

    return self::$models;
  }
  
  public static function getAllModels()
  {
    if (null === self::$allModels)
    {
      $baseModels = sfFinder::type('file')
      ->name("Base*.class.php")
      ->in(dmOs::join(sfConfig::get("sf_lib_dir"), "model/doctrine"));

      self::$allModels = array();
      foreach($baseModels as $baseModel)
      {
        self::$allModels[] = preg_replace('|^Base(\w+).class.php$|', '$1', basename($baseModel));
      }
    }

    return self::$allModels;
  }
  
  public static function getDmModels()
  {
    if (null === self::$dmModels)
    {
      $baseModels = sfFinder::type('file')
      ->name("Base*.class.php")
      ->maxDepth(0)
      ->in(dmOs::join(sfConfig::get("sf_lib_dir"), "model/doctrine/dmCorePlugin/base"));

      self::$dmModels = array();
      foreach($baseModels as $baseModel)
      {
        self::$dmModels[] = preg_replace('|^Base(\w+).class.php$|', '$1', basename($baseModel));
      }
    }
    
    return self::$dmModels;
  }

  public static function checkFilesystemPermissions()
  {
    $requiredWritableDirs = array(
      sfConfig::get('sf_cache_dir'),
      sfConfig::get('dm_cache_dir'),
      sfConfig::get('sf_log_dir'),
//      sfConfig::get('dm_data_dir'),
//      dmOs::join(sfConfig::get('dm_data_dir'), 'backup'),
//      dmOs::join(sfConfig::get('dm_data_dir'), 'index'),
//      dmOs::join(sfConfig::get('dm_data_dir'), 'log'),
      sfConfig::get('sf_upload_dir'),
//      dmOs::join(sfConfig::get('sf_lib_dir'), 'migration/doctrine')
    );

    $messages = array();

    foreach($requiredWritableDirs as $requiredWritableDir)
    {
      if (!is_dir($requiredWritableDir))
      {
        $oldUmask = umask(0);
        @mkdir($requiredWritableDir, 0777, true);
        umask($oldUmask);
      }
    
      if(!is_writable($requiredWritableDir))
      {
        $messages[] = sprintf(
          'Folder %s should be writable',
          str_replace(sfConfig::get('sf_root_dir'), '', $requiredWritableDir)
        );
      }
    }

    if(count($messages))
    {
      dm::getUser()->logAlert(implode("\n", $messages), false);
    }
  }
  
  public static function getRootDir()
  {
    return dmOs::normalize(sfConfig::get('sf_root_dir'));
  }
  
  /*
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
  
  /*
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
    return strpos(dmOs::normalize($path), self::getRootDir().'/') === 0;
  }
  
  public static function appExists($application)
  {
    return file_exists(self::rootify('apps/'.$application.'/config/'.$application.'Configuration.class.php'));
  }
}