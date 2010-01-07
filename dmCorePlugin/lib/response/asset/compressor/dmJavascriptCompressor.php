<?php

class dmJavascriptCompressor extends dmAssetCompressor
{
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
      $content = dmJsMinifier::transform($content);
    }
    
    return $content.';';
  }

  protected function isMinifiable($path)
  {
    return $this->options['minify'] && $this->dispatcher->filter(
      new sfEvent($this, 'dm.javascript_compressor.minifiable', array('path' => $path)),
      !strpos($path, '.min.') && !strpos($path, '.pack.')
    )->getReturnValue();
  }
  
  protected function isCachable($javascript, array $options = array())
  {
    if($this->options['protect_user_assets'] && strncmp($javascript, '/dm', 3) !== 0)
    {
      return false;
    }
    
    return true;
  }
}