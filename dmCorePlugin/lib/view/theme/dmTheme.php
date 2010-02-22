<?php

class dmTheme
{
  protected
    $dispatcher,
    $filesystem,
    $dir,
    $name,
    $enabled,
    $requestContext;

  public function __construct(sfEventDispatcher $dispatcher, dmFilesystem $filesystem, array $requestContext, array $options)
  {
    $this->dispatcher       = $dispatcher;
    $this->filesystem       = $filesystem;
    $this->requestContext   = $requestContext;
    
    $this->initialize($options);
  }

  public function initialize(array $options)
  {
    if(!isset($options['dir']))
    {
      throw new dmException('You must provide a theme dir for '.$options['name']);
    }
    
    $this->dir      = trim($options['dir'], '/');
    $this->name     = $options['name'];
    $this->enabled  = $options['enabled'];
    
    $this->connect();
  }
  
  protected function connect()
  {
    $this->dispatcher->connect('dm.refresh', array($this, 'listenToDmRefreshEvent'));
  }
  
  public function listenToDmRefreshEvent(sfEvent $event)
  {
    if (!$this->exists())
    {
      try
      {
        $this->create();
      }
      catch(Exception $e)
      {
        $message = $e->getMessage();
        
        $this->dispatcher->notify(new sfEvent($this, 'application.log', array('priority' => sfLogger::ERR, $message)));
        
        $event->getSubject()->getUser()->logError($message);
        
        if (sfConfig::get('dm_debug'))
        {
          throw $e;
        }
      }
    }
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
    foreach($this->getFullPaths() as $fullPath)
    {
      @$this->filesystem->mkdir($fullPath);
      
      if (!is_dir($fullPath))
      {
        throw new dmException(sprintf(
          '%s can not be created. Please check %s permissions',
          dmProject::unRootify($fullPath),
          dmProject::unRootify(dirname($fullPath))
        ));
      }
    }

    $this->filesystem->touch($this->getFullPath('css/markdown.css'));
    
    $this->dispatcher->notify(new sfEvent($this, 'dm.theme.created', array()));
  }

  public function getName()
  {
    return $this->name;
  }
  
  public function isEnabled()
  {
    return $this->enabled;
  }

  public function getBasePath()
  {
    return sfConfig::get('sf_web_dir');
  }

  public function getFullPath($path = null)
  {
    return $this->getBasePath().$this->getPath($path);
  }

  /**
   * public path
   * example : /theme/css/style.css or /public_html/theme/css/style.css if no virtual host
   */
  public function getWebPath($path = null)
  {
    return $this->requestContext['relative_url_root'].$this->getPath($path);
  }
  
  /**
   * full public path
   * example : http://mysite.com/theme/css/style.css or http://localhost/mysite/public_html/theme/css/style.css if no virtual host
   */
  public function getFullWebPath($path = null)
  {
    return $this->requestContext['absolute_url_root'].$this->getPath($path);
  }
  
  /**
   * path from web dir
   * example : /theme/css/style.css
   */
  public function getPath($path = '')
  {
    return '/'.$this->dir.'/'.trim($path, '/');
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
    return $this->name;
  }

}