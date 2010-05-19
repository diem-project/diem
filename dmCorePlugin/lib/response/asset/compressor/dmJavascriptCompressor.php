<?php

class dmJavascriptCompressor extends dmAssetCompressor
{

  public function getDefaultOptions()
  {
    return array_merge(parent::getDefaultOptions(), array(
      'black_list'          => array()
    ));
  }

  public function preProcess()
  {
    parent::preProcess();

    // remove head included javascripts from compression
    foreach($this->assets as $webPath => $options)
    {
      if (!empty($options['head_inclusion']))
      {
        unset($this->assets[$webPath]);
      }
    }
  }

  public function addToBlackList($fileName)
  {
    $this->mergeOption('black_list', (array) $fileName);
  }

  public function connect()
  {
    $this->dispatcher->connect('dm.layout.filter_javascripts', array($this, 'listenToFilterAssetsEvent'));
  }
  
  protected function getType()
  {
    return 'js';
  }
  
  protected function processAssetContent($content, $path)
  {
    if ($this->isMinifiable($path))
    {
      try
      {
        $content = dmJsMinifier::transform($content);
      }
      catch(JsMinEnhException $e)
      {
        $this->dispatcher->notify(new sfEvent($this, 'application.log', array(
          'priority' => sfLogger::ERR,
          sprintf('Javascript compression failed for %s: %s', $path, $e->getMessage())
        )));
      }
    }
    
    return $content.';';
  }

  protected function isMinifiable($path)
  {
    return $this->getOption('minify') && $this->dispatcher->filter(
      new sfEvent($this, 'dm.javascript_compressor.minifiable', array('path' => $path)),
      !in_array(basename($path), $this->getOption('black_list')) && !strpos($path, '.min.') && !strpos($path, '.pack.')
    )->getReturnValue();
  }

  protected function isCachable($path, array $options = array())
  {
    return $this->options['minify'] && $this->dispatcher->filter(
      new sfEvent($this, 'dm.javascript_compressor.cachable', array('path' => $path, 'options' => $options)),
      !in_array(basename($path), $this->getOption('black_list')) && !($this->options['protect_user_assets'] && strncmp($path, '/dm', 3) !== 0)
    )->getReturnValue();
  }
}