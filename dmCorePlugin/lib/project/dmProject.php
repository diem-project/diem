<?php

class dmProject
{

  protected static
    $key,
    $models;

  /*
   * Returns project key based on his dir_name
   */
  public static function getKey()
  {
    if (self::$key === null)
    {
      self::$key = basename(sfConfig::get("sf_root_dir"));
    }
    return self::$key;
  }

  public static function getModels()
  {
    if (is_null(self::$models))
    {
      $baseModels = sfFinder::type('file')
      ->maxdepth(0)
      ->name("Base*.class.php")
      ->in(dmOs::join(sfConfig::get("sf_lib_dir"), "model/doctrine/base"), dmOs::join(sfConfig::get("sf_lib_dir"), "model/doctrine/generated"));

      self::$models = array();
      foreach($baseModels as $baseModel)
      {
        self::$models[] = preg_replace('|^Base(\w+).class.php$|', '$1', basename($baseModel));
      }
    }

    return self::$models;
  }

  public static function checkFilesystemPermissions()
  {
    $requiredWritableDirs = array(
      sfConfig::get('sf_cache_dir'),
      sfConfig::get('sf_data_dir'),
      sfConfig::get('sf_upload_dir'),
      dmOs::join(sfConfig::get('sf_cache_dir'), 'dm'),
      dmOs::join(sfConfig::get('sf_lib_dir'), 'migration/doctrine'),
      dmOs::join(sfConfig::get('sf_data_dir'), 'backup')
    );
    
    $fs = dmFilesystem::get();

    $messages = array();

    foreach($requiredWritableDirs as $requiredWritableDir)
    {
      if(!$fs->mkdir($requiredWritableDir))
      {
        $messages[] = sprintf(
          'Folder %s should be writable',
          str_replace(sfConfig::get('sf_root_dir'), '', $requiredWritableDir)
        );
      }
    }

    if(count($messages))
    {
      dm::getUser()->logAlert(implode("\n", $messages));
    }
  }
  
  public static function getRootDir()
  {
  	return sfConfig::get('sf_root_dir');
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
    return strpos($path, self::getRootDir().'/') === 0;
  }
  
  public static function appExists($application)
  {
  	return file_exists(self::rootify('apps/'.$application.'/config/'.$application.'Configuration.class.php'));
  }
}