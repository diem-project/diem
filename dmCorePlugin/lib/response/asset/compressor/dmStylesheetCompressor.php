<?php

class dmStylesheetCompressor extends dmAssetCompressor
{
  public function connect()
  {
    $this->dispatcher->connect('dm.layout.filter_stylesheets', array($this, 'listenToFilterAssetsEvent'));
  }
  
  protected function getType()
  {
    return 'css';
  }
  
  protected function isCachable($stylesheet, array $options = array())
  {
    // don't cache user asset if it's protected
    if($this->options['protect_user_assets'] && strncmp($stylesheet, '/dm', 3) !== 0)
    {
      return false;
    }

    // don't cache stylesheet if included with any condition
    if(isset($options['condition']))
    {
      return false;
    }

    // don't cache stylesheet if it contains an @import rule
    if(false !== strpos(file_get_contents(sfConfig::get('sf_web_dir').$stylesheet), '@import'))
    {
      return false;
    }

    return true;
  }
  
  protected function processCacheKey($cacheKey)
  {
    return $cacheKey.$this->requestContext['relative_url_root'];
  }
  
  protected function processAssetContent($content, $path)
  {
    return $this->fixCssPaths($content, $path);
  }
  
  protected function processCacheContent($content)
  {
    return $this->options['minify'] ? dmCssMinifier::transform($content) : $content;
  }

  public function fixCssPaths($content, $path)
  {
    if (preg_match_all("/url\(\s?[\'|\"]?(.+)[\'|\"]?\s?\)/Uix", $content, $urlMatches))
    {
      $urlMatches = array_unique( $urlMatches[1] );
      $cssPathArray = explode('/', $path);
      
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
          $newMatchPath = $this->requestContext['relative_url_root'].implode('/', $cssPathSlice) . '/' . str_replace('../', '', $match);
          $content = str_replace( $match, $newMatchPath, $content );
        }
      }
    }
    
    return $content;
  }
}
