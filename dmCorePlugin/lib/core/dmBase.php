<?php

define('DIEM_VERSION', '5.1.0-BETA');

// include symfony
if (!defined('SYMFONY_VERSION'))
{
  require_once realpath(dirname(__FILE__).'/../../..').'/symfony/lib/autoload/sfCoreAutoload.class.php';
  sfCoreAutoload::register();
}

class dmBase
{

  protected static
  $startTime,
  $version,
  $dir;

  public static function start($dir = null)
  {
    if (null !== self::$dir)
    {
      throw new Exception('Diem has already been started');
    }
    
    self::resetStartTime();

    self::$version = DIEM_VERSION;

    self::$dir = null === $dir ? realpath(dirname(__FILE__).'/../../..') : $dir;

    require_once(self::$dir.'/dmCorePlugin/lib/config/dmProjectConfiguration.php');
  }
  
  public static function resetStartTime()
  {
    self::$startTime = microtime(true);
  }
  
  public static function getDir()
  {
    return self::$dir;
  }
  
  public static function getStartTime()
  {
    return self::$startTime;
  }

  /**
   * Loads the Swift mailer
   */
  public static function enableMailer()
  {
    if(!class_exists('Swift_Message'))
    {
      Swift::registerAutoload();
      sfMailer::initialize();
    }
  }
  
  /**
   * All context creations are made here.
   * You can replace here the dmContext class by your own.
   */
  public static function createContext(sfApplicationConfiguration $configuration, $name = null, $class = 'dmContext')
  {
    return dmContext::createInstance($configuration, $name, $class);
  }
  
  public static function checkServer()
  {
    require_once(realpath(dirname(__FILE__).'/../os/dmServerCheck.php'));
    
    $serverCheck = new dmServerCheck;
    
    print $serverCheck->render();
    
    exit;
  }

  public static function setVersion($version)
  {
    self::$version = $version;
  }

  public static function getVersion()
  {
    return self::$version;
  }

  public static function getVersionMajor()
  {
    $parts = explode('.', self::getVersion());
    return $parts[0];
  }

  public static function getVersionMinor()
  {
    $parts = explode('.', self::getVersion());
    return $parts[1];
  }

  public static function getVersionMaintenance()
  {
    $parts = explode('.', self::getVersion());
    return $parts[2];
  }

  public static function getVersionBranch()
  {
    $parts = explode('.', self::getVersion());
    return $parts[0].'.'.$parts[1];
  }
  
  /**
   * Symfony common objects accessors
   */

  public static function getRouting()
  {
    return dmContext::getInstance()->getRouting();
  }

  /**
   * @return dmWebRequest
   */
  public static function getRequest()
  {
    return dmContext::getInstance()->getRequest();
  }

  public static function getResponse()
  {
    return dmContext::getInstance()->getResponse();
  }

  public static function getController()
  {
    return dmContext::getInstance()->getController();
  }

  public static function getEventDispatcher()
  {
    return dmContext::hasInstance()
    ? dmContext::getInstance()->getEventDispatcher()
    : ProjectConfiguration::getActive()->getEventDispatcher();
  }

  public static function getUser()
  {
    return dmContext::getInstance()->getUser();
  }

  public static function getI18n()
  {
    return dmContext::getInstance()->getI18n();
  }
  
  public static function loadHelpers($helpers)
  {
    return dmContext::getInstance()->getConfiguration()->loadHelpers($helpers);
  }
  
  public static function getHelper()
  {
    return dmContext::getInstance()->getHelper();
  }

  /**
   * Gadgets
   */

  /**
   * Diem code size
   * returns array(files, lines, characters)
   */
  public static function getDiemSize()
  {
    $timer = dmDebug::timerOrNull('dm::getDiemSize()');

    $pluginsDir = sfConfig::get('sf_plugins_dir').'/';

    $files = sfFinder::type('file')
      ->prune('om')
      ->prune('map')
      ->prune('base')
      ->prune('vendor')
      ->name('*\.php', '*\.css', '*\.js', '*\.yml')
      ->in($pluginsDir.'dmCorePlugin', $pluginsDir.'dmAdminPlugin', $pluginsDir.'dmFrontPlugin');

    foreach($files as $key => $file)
    {
      if(strpos($file, '/web/lib/'))
      {
        unset($files[$key]);
      }
    }

    $lines = 0;
    $characters = 0;

    foreach($files as $file)
    {
      $content = file($file);
      $lines += count($content);
      $characters += strlen(implode(' ', $content));
    }

    $response = array(
      'nb_files' => count($files),
      'lines' => $lines,
      'characters' => $characters
    );

    $timer && $timer->addTime();

    return $response;
  }
}
