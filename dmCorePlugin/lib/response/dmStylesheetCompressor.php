<?php

class dmStylesheetCompressor extends dmAssetCompressor
{
  protected
  $relativeUrlRoot;
  
  public function connect()
  {
    $this->dispatcher->connect('dm.response.filter_stylesheets', array($this, 'listenFilterAssets'));
  }
  
  protected function getType()
  {
    return 'css';
  }
  
  protected function preProcess()
  {
    $this->relativeUrlRoot = $this->request->getRelativeUrlRoot();
  }
  
  protected function isCachable($stylesheet, array $options = array())
  {
    return true;
  }
  
  protected function processCacheKey($cacheKey)
  {
    return $cacheKey.$this->relativeUrlRoot;
  }
  
  protected function processAssetContent($content, $path)
  {
    return $this->fixCssPaths($content, $path);
  }
  
  protected function processCacheContent($content)
  {
    return $this->options['minify'] ? dmCssMinifier::transform($content) : $content;
  }

  protected function fixCssPaths($content, $path)
  {
    if (preg_match_all("/url\(\s?[\'|\"]?(.+)[\'|\"]?\s?\)/ix", $content, $urlMatches))
    {
      $urlMatches = array_unique( $urlMatches[1] );
      $cssPathArray = explode(DIRECTORY_SEPARATOR, $path);

      //aze::trace($path, $content);

      // pop the css file name
      array_pop( $cssPathArray );
      $cssPathCount   = count( $cssPathArray );

      foreach( $urlMatches as $match )
      {
        $match = str_replace( array('"', "'"), '', $match );
        // replace path if it is relative
        if ( $match[0] !== '/' && strpos( $match, 'http:' ) === false )
        {
          $relativeCount = substr_count( $match, '../' );
          $cssPathSlice = $relativeCount === 0 ? $cssPathArray : array_slice( $cssPathArray  , 0, $cssPathCount - $relativeCount  );
          $newMatchPath = $this->relativeUrlRoot.implode('/', $cssPathSlice) . '/' . str_replace('../', '', $match);
          $content = str_replace( $match, $newMatchPath, $content );
        }
      }
    }
    
    return $content;
  }
}