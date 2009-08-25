<?php

class dmTheme extends dmMicroCache
{
	protected static
	  $instances;

	protected
	  $key,
	  $name,
	  $baseWebPath;

	public static function getList()
	{
    if(isset(self::$instances))
    {
    	return self::$instances;
    }

    self::$instances = array();

    foreach(sfConfig::get('dm_theme_list') as $themeKey => $themeName)
    {
      self::$instances[$themeKey] = new dmTheme($themeKey, $themeName);
    }
    
    return self::$instances;
	}

	public static function getTheme($key)
	{
		return dmArray::get(self::getList(), $key);
	}

	public function __construct($key, $name)
	{
		$this->key = $key;
		$this->name = $name;

		$this->initialize();
	}
	
	protected function initialize()
	{
    if (!$this->exists())
    {
      try
      {
        $this->create();
      }
      catch(dmException $e)
      {
        if (sfConfig::get('sf_debug'))
        {
          throw $e;
        }
        dm::getUser()->logAlert(sprintf(
          'Theme %s could not be created. Please check %s permissions',
          $this->getKey(),
          $this->getBasePath()
        ));
      }
    }
    
    $this->baseWebPath = dm::getRequest()->getRelativeUrlRoot();
	}

  public function exists()
  {
    foreach($this->getFullPaths() as $fullPath)
    {
      if(!is_dir($fullPath))
      {
        return false;
      }
    }

    return true;
  }

  public function create()
  {
  	$fs = dmFileSystem::get();

    foreach($this->getFullPaths() as $fullPath)
    {
      if (!$fs->mkdir($fullPath))
      {
      	throw new dmException(sprintf(
          '%s can not be created',
      	  $fullPath
      	));
      }
    }

    $fs->touch($this->getFullPath('css/markdown.css'));
  }

	public function getKey()
	{
		return $this->key;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getBasePath()
	{
		return sfConfig::get('sf_web_dir');
	}

  public function getFullPath($path = null)
  {
    return dmOs::join($this->getBasePath(), $this->getPath($path));
  }

  
  /*
   * public path
   * example : /theme/css/style.css or /public_html/theme/css/style.css if no virtual host
   */
  public function getWebPath($path = null)
  {
  	return $this->baseWebPath.$this->getPath($path);
  }
  
  /*
   * path from web dir
   * example : /theme/css/style.css
   */
  public function getPath($path = '')
  {
    return dmOs::join($this->getKey(), trim($path, '/'));
  }

  public function getFullPaths()
  {
  	$fullPaths = array();
  	foreach(array(null, 'css', 'images') as $path)
  	{
  		$fullPaths[] = $this->getFullPath($path);
  	}

  	return $fullPaths;
  }

  public function __toString()
  {
  	return $this->getName();
  }

}