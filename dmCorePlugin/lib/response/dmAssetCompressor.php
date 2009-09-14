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
      'gz_compression'  => true,
      'minify'          => true
    ), $options);
    
    $this->type = $this->getType();
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
    $this->processedAssets  = array();
    $this->cacheKey         = '';
    $this->webDir           = sfConfig::get('sf_web_dir');

    $this->preProcess();

    foreach($this->assets as $webPath => $options)
    {
      if ($this->isCachable($webPath, $options))
      {
        $this->cacheKey .= $webPath;
        
        if (!file_exists($this->webDir.$webPath))
        {
          $this->log('Asset does not exist : '.$this->webDir.$webPath);
          unset($this->assets[$webPath]);
        }
        else
        {
          $this->cacheKey .= filemtime($this->webDir.$webPath);
        }
      }
      else
      {
        $this->processedAssets[$webPath] = $options;
        unset($this->assets[$webPath]);
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
      
      foreach($this->assets as $webPath => $options)
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
    
    $this->processedAssets = array_merge($this->processedAssets, array($cacheWebPath.'/'.$this->cacheKey.'.'.$this->type => array()));
    
    $this->postProcess();
    
    if ($timer) $timer->addTime();
    
    return $this->processedAssets;
  }
  
  protected function log($message)
  {
    $this->dispatcher->notify(new sfEvent($this, 'application.log', array('priority' => sfLogger::WARNING, $message)));
  }
}