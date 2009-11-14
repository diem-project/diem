<?php

class dmTheme
{
  protected
    $dispatcher,
    $filesystem,
    $key,
    $path,
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
    if(!isset($options['key']) || !isset($options['path']))
    {
      throw new dmException('You must provide both key and path');
    }
    
    $this->key      = $options['key'];
    $this->path     = trim($options['path'], '/');
    $this->name     = dmArray::get($options, 'name', dmString::humanize($options['key']));
    $this->enabled  = dmArray::get($options, 'enabled', true);
    
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
        $message = sprintf(
          'Theme %s can not be created. Please check %s permissions',
          $this->path,
          $this->getBasePath()
        );
        
        $this->dispatcher->notify(new sfEvent($this, 'application.log', array('priority' => sfLogger::ERR, $message)));
        
        $event->getSubject()->getUser()->logError($message);
        
        if (sfConfig::get('sf_debug'))
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
      if (!$this->filesystem->mkdir($fullPath))
      {
        throw new dmException(sprintf('%s can not be created', $fullPath));
      }
    }

    $this->filesystem->touch($this->getFullPath('css/markdown.css'));
    
    $this->dispatcher->notify(new sfEvent($this, 'dm.theme.created', $this));
  }

  public function getKey()
  {
    return $this->key;
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

  /*
   * public path
   * example : /theme/css/style.css or /public_html/theme/css/style.css if no virtual host
   */
  public function getWebPath($path = null)
  {
    return $this->requestContext['relative_url_root'].$this->getPath($path);
  }
  
  /*
   * full public path
   * example : http://mysite.com/theme/css/style.css or http://localhost/mysite/public_html/theme/css/style.css if no virtual host
   */
  public function getFullWebPath($path = null)
  {
    return $this->requestContext['absolute_url_root'].$this->getPath($path);
  }
  
  /*
   * path from web dir
   * example : /theme/css/style.css
   */
  public function getPath($path = '')
  {
    return '/'.$this->path.'/'.trim($path, '/');
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