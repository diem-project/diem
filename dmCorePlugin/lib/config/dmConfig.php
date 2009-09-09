<?php

/**
 * dmConfig stores all configuration information for a diem application in database.
 */
class dmConfig
{
  protected static
  $culture,
  $config,
  $loaded = false;

  /**
   * Retrieves a config parameter.
   *
   * @param string $name    A config parameter name
   * @param mixed  $default A default config parameter value
   *
   * @return mixed A config parameter value, if the config parameter exists, otherwise null
   */
  public static function get($name)
  {
    if (!self::has($name))
    {
      throw new dmException(sprintf('There is no setting called "%s". Available settings are : %s', $name, implode(', ', array_keys(self::$config))));
    }
    return self::$config[$name];
  }

  /**
   * Indicates whether or not a config parameter exists.
   *
   * @param string $name A config parameter name
   *
   * @return bool true, if the config parameter exists, otherwise false
   */
  public static function has($name)
  {
    if(!self::$loaded)
    {
      self::load();
    }
    return array_key_exists($name, self::$config);
  }

  /**
   * Sets a config parameter.
   *
   * If a config parameter with the name already exists the value will be overridden.
   *
   * @param string $name  A config parameter name
   * @param mixed  $value A config parameter value
   */
  public static function set($name, $value)
  {
    if (!self::has($name))
    {
      throw new dmException(sprintf('There is no setting called "%s". Available settings are : %s', $name, implode(', ', array_keys(self::$config))));
    }
    
    $setting = dmDb::query('DmSetting s')->where('s.name = ?', $name)->withI18n(self::$culture)->fetchOne();
    
    $setting->value = $value;
    
    $setting->save();
//    
//    $stmt = Doctrine_Manager::connection()->prepare('SELECT COUNT(t.id)
//FROM dm_setting s
//LEFT JOIN dm_setting_translation t ON t.id=s.id AND t.lang=?
//WHERE s.name=?');
//    
//    $stmt->execute(array(self::$culture, $name));
//    
//    $exists = $stmt->fetchColumn();
//    
//    
//    if($exists)
//    {
//      $stmt = Doctrine_Manager::connection()->prepare('UPDATE dm_setting s
//  LEFT JOIN dm_setting_translation t ON t.id=s.id AND t.lang=?
//  SET t.value=?
//  WHERE s.name=?');
//      $stmt->execute(array(self::$culture, $value, $name));
//    }
//    else
//    {
//      $stmt = Doctrine_Manager::connection()->prepare('INSERT dm_setting s
//  LEFT JOIN dm_setting_translation t ON t.id=s.id AND t.lang=?
//  SET t.value=?
//  WHERE s.name=?');
//      $stmt->execute(array(self::$culture, $value, $name));
//    }
    
    return self::$config[$name] = $value;
  }

  /**
   * Retrieves all configuration parameters.
   *
   * @return array An associative array of configuration parameters.
   */
  public static function getAll()
  {
    if(!self::$loaded)
    {
      self::load();
    }
    return self::$config;
  }
  
  public static function initialize(sfEventDispatcher $dispatcher)
  {
    self::$config = array();
    
    $dispatcher->connect('user.change_culture', array('myConfig', 'listenToChangeCultureEvent'));
  }

  /**
   * Listens to the user.change_culture event.
   *
   * @param sfEvent An sfEvent instance
   */
  public static function listenToChangeCultureEvent(sfEvent $event)
  {
    if (self::$culture != $event['culture'])
    {
      self::$culture = $event['culture'];
      self::load();
    }
  }
  
  public static function load($useCache = true)
  {
    $timer = dmDebug::timer('load config');
    
    if (!self::$culture)
    {
      if (class_exists('sfContext', false) && sfContext::hasInstance() && $user = sfContext::getInstance()->getUser())
      {
        self::$culture = $user->getCulture();
      }
      else
      {
        self::$culture = sfConfig::get('sf_default_culture');
      }
    }
    
    $query = dmDb::query('DmSetting s')
    ->leftJoin('s.Translation t ON s.id = t.id AND t.lang IN (?, ?)', array(self::$culture, sfConfig::get('sf_default_culture')))
    ->select('s.name, t.value');
    
    if ($useCache)
    {
      $query->dmCache();
    }
    
    $_settings = $query->fetchPDO();
    
    $settings = array();
    foreach($_settings as $_setting)
    {
      $settings[$_setting[0]] = $_setting[1];
    }
    
    self::$config = $settings;
    
    self::$loaded = true;
    
    $timer->addTime();
  }
  
  public static function getCulture()
  {
    return self::$culture;
  }

	public static function isCli()
	{
    return !isset($_SERVER['HTTP_HOST']);
	}

}