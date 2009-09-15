<?php

abstract class dmAssetCompressor
{
  protected
  $dispatcher,
  $filesystem,
  $relativeUrlRoot,
  $type,
  $assets,
  $processedAssets,
  $cacheKey,
  $webDir;
  
  public function __construct(sfEventDispatcher $dispatcher, dmFilesystem $filesystem, $relativeUrlRoot, array $options = array())
  {
    $this->dispatcher       = $dispatcher;
    $this->filesystem       = $filesystem;
    $this->relativeUrlRoot  = $relativeUrlRoot;
    
    $this->initialize($options);
  }
  
  public function initialize(array $options = array())
  {
    $this->options = array_merge(array(
      'gz_compression'      => true,
      'minify'              => true,
      'protect_user_assets' => false
    ), $options);
    
    $this->type = $this->getType();
  }
  
  public function setOption($name, $value)
  {
    $this->options[$name] = $value;
  }
  
  public function listenFilterAssets(sfEvent $event, array $assets)
  {
    return $this->process($assets);
  }
  
  abstract protected function getType();
  
  abstract public function connect();
  
  protected function processAssetContent($content, $path)
  {
    return $content;
  }
  
  protected function processCacheContent($content)
  {
    return $content;
  }

  protected function isCachable($asset, array $options = array())
  {
    return true;
  }

  protected function processCacheKey($cacheKey)
  {
    return $this->cacheKey;
  }
  
  protected function preProcess()
  {
  }
  
  protected function postProcess()
  {
  }
  
  public function process(array $assets)
  {
    $timer = dmDebug::timerOrNull('dmAssetCompressor::process('.$this->type.')');
    
    $this->assets           = $assets;
    $this->cachedAssets     = array();
    $this->cdnAssets  = array();
    $this->preservedAssets  = array();
    $this->processedAssets  = array();
    $this->cacheKey         = '';
    $this->webDir           = sfConfig::get('sf_web_dir');

    $this->preProcess();

    foreach($this->assets as $webPath => $options)
    {
      if ($this->isOnFilesystem($webPath))
      {
        if (!file_exists($this->webDir.$webPath))
        {
          $this->log('Missing '.$this->type.' : '.$this->webDir.$webPath);
          $this->cacheKey .= $webPath;
        }
        elseif ($this->isCachable($webPath, $options))
        {
          $this->cachedAssets[$webPath] = $options;
          $this->cacheKey .= $webPath.filemtime($this->webDir.$webPath);
        }
        else
        {
          $this->preservedAssets[$webPath] = $options;
        }
      }
      else
      {
        $this->cdnAssets[$webPath] = $options;
      }
    }

    $this->cacheKey = md5($this->processCacheKey($this->cacheKey));

    $cacheWebPath = '/cache/'.$this->type;
    $cacheDirPath = $this->webDir.$cacheWebPath;
    $cacheFilePath = $cacheDirPath.'/'.$this->cacheKey.'.'.$this->type;
    
    $this->filesystem->mkdir(sfConfig::get('sf_cache_dir').'/web');
    $this->filesystem->mkdir(sfConfig::get('sf_cache_dir').'/web/'.$this->type);

    if(!file_exists($cacheFilePath))
    {
      $cacheContent = '';
      
      foreach($this->cachedAssets as $webPath => $options)
      {
        $cacheContent .= $this->processAssetContent(file_get_contents($this->webDir.$webPath), $webPath);
      }

      $cacheContent = $this->processCacheContent($cacheContent);

      file_put_contents($cacheFilePath, $cacheContent);
      chmod($cacheFilePath, 0666);
      
      if ($this->options['gz_compression'])
      {
        file_put_contents($cacheFilePath.'.gz', gzencode($cacheContent));
        chmod($cacheFilePath.'.gz', 0666);
      }
    }
    
    $this->processedAssets = array_merge(
      $this->cdnAssets,
      array($cacheWebPath.'/'.$this->cacheKey.'.'.$this->type => array()),
      $this->preservedAssets
    );
    
    $this->postProcess();
    
    $timer && $timer->addTime();
    
    return $this->processedAssets;
  }
  
  protected function isOnFilesystem($asset)
  {
    return $asset{0} === '/';
  }
  
  protected function log($message)
  {
    $this->dispatcher->notify(new sfEvent($this, 'application.log', array('priority' => sfLogger::WARNING, $message)));
  }
}