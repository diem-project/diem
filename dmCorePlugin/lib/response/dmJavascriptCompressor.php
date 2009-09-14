<?php

class dmJavascriptCompressor extends dmAssetCompressor
{
  public function connect()
  {
    $this->dispatcher->connect('dm.response.filter_javascripts', array($this, 'listenFilterAssets'));
  }
  
  protected function getType()
  {
    return 'js';
  }
  
  protected function processAssetContent($content, $path)
  {
    if ($this->options['minify'] && !strpos($path, '.min.') && !strpos($path, '.pack.'))
    {
      $content = dmJsMinifier::transform($content).';';
    }
    
    return $content;
  }
  
  protected function isCachable($javascript, array $options = array())
  {
    return $javascript{0} === '/';
  }
}