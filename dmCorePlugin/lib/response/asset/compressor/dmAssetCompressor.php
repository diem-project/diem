<?php

abstract class dmAssetCompressor extends dmConfigurable
{
  protected
  $dispatcher,
  $filesystem,
  $requestContext,
  $type,
  $assets,
  $processedAssets,
  $cachedAssetsPaths,
  $cacheKeys,
  $webDir;
  
  public function __construct(sfEventDispatcher $dispatcher, dmFilesystem $filesystem, array $requestContext, array $options = array())
  {
    $this->dispatcher       = $dispatcher;
    $this->filesystem       = $filesystem;
    $this->requestContext   = $requestContext;
    
    $this->initialize($options);
  }
  
  public function initialize(array $options = array())
  {
    $this->configure($options);
        
    $this->type = $this->getType();
  }
  
  public function getDefaultOptions()
  {
    return array(
      'gz_compression'      => true,
      'minify'              => true,
      'protect_user_assets' => false
    );
  }
  
  public function listenToFilterAssetsEvent(sfEvent $event, array $assets)
  {
    if ($this->isEnabled())
    {
      return $this->process($assets);
    }
    
    return $assets;
  }
  
  public function isEnabled()
  {
    return sfConfig::get('dm_'.$this->getType().'_compress', true);
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
    return $cacheKey;
  }
  
  protected function preProcess()
  {
  }
  
  protected function postProcess()
  {
  }
  
  public function process(array $assets)
  {
    if (empty($assets))
    {
      return array();
    }
    
    $timer = dmDebug::timerOrNull('dmAssetCompressor::process('.$this->type.')');
    
    $this->assets           = $assets;
    $this->cachedAssets     = array();
    $this->cachedAssetsPaths= array();
    $this->cdnAssets        = array();
    $this->preservedAssets  = array();
    $this->processedAssets  = array();
    $this->cacheKeys        = array();
    $this->webDir           = sfConfig::get('sf_web_dir');

    $this->preProcess();
    
    foreach($this->assets as $webPath => $options)
    {
      if ($this->isOnFilesystem($webPath))
      {
        if (!file_exists($this->webDir.$webPath))
        {
          $this->log('Missing '.$this->type.' : '.$this->webDir.$webPath);
          $this->cacheKeys['all'] .= $webPath;
        }
        elseif ($this->isCachable($webPath, $options))
        {
          $mediaType=isset($options['media']) ? $options['media'] : 'all';
          $options['media']=$mediaType;
          $this->cachedAssets[$mediaType][$webPath] = $options;

          if(!isset($this->cacheKeys[$mediaType]))
          {
            $this->cacheKeys[$mediaType]=''; 
          }
          $this->cacheKeys[$mediaType] .= $webPath.filemtime($this->webDir.$webPath);
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

    if (!empty($this->cachedAssets))
    {

      $cachedAssetsPaths=array();

      foreach($this->cachedAssets as $mediaType => $cachedAssets)
      {
        $this->cacheKeys[$mediaType] = md5($this->processCacheKey($this->cacheKeys[$mediaType]));

        $cacheWebPath = '/cache/'.$this->type;
        $cacheDirPath = $this->webDir.$cacheWebPath;
        $cacheFilePath = $cacheDirPath.'/'.$this->cacheKeys[$mediaType].'.'.$this->type;

        $this->filesystem->mkdir($cacheDirPath);

        if(!file_exists($cacheFilePath))
        {
          $cacheContent = '';

          foreach($cachedAssets as $webPath => $options)
          {
            $cacheContent .= $this->processAssetContent(file_get_contents($this->webDir.$webPath), $webPath);
            $this->cachedAssetsPaths[$cacheWebPath.'/'.$this->cacheKeys[$mediaType].'.'.$this->type] = $options;
          }

          $cacheContent = $this->processCacheContent($cacheContent);

          file_put_contents($cacheFilePath, $cacheContent);
          chmod($cacheFilePath, 0666);

          if ($this->options['gz_compression'])
          {
            file_put_contents($cacheFilePath.'.gz', gzencode($cacheContent));
            chmod($cacheFilePath.'.gz', 0666);
          }



          $message = sprintf('%s : compressed %d assets ( %s )', get_class($this), count($cachedAssets), dmOs::humanizeSize($cacheFilePath));
          $this->dispatcher->notify(new sfEvent($this, 'application.log', array($message, 'priority' => sfLogger::INFO)));

        }
        else
        {
          $this->cachedAssetsPaths[$cacheWebPath.'/'.$this->cacheKeys[$mediaType].'.'.$this->type] = array('media'=>$mediaType);
        }


      }
      $this->processedAssets = array_merge(
          $this->cdnAssets,
          $this->cachedAssetsPaths,
          $this->preservedAssets
        );
    }
    else
    {
      $this->processedAssets = array_merge(
        $this->cdnAssets,
        $this->preservedAssets
      );
    }
    
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
