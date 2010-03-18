<?php

class dmScriptNameResolver
{
  protected
  $requestContext,
  $culture;
  
  public function __construct(array $requestContext, $culture)
  {
    $this->requestContext = $requestContext;
    $this->culture        = $culture;
  }
  
  public function get($app = null, $env = null, $culture = null)
  {
    $app = null === $app ? sfConfig::get('sf_app') : $app;
    $env = null === $env ? sfConfig::get('sf_environment') : $env;
    $culture = null === $culture ? $this->culture : $culture;

    if($config = dmConfig::get('base_urls'))
    {
      $knownAppUrls = json_decode($config, true);
    }
    else
    {
      $knownAppUrls = array();
    }

    $appUrlKey = implode('-', array($app, $env, $culture));

    if (!($appUrl = dmArray::get($knownAppUrls, $appUrlKey)))
    {
      if (!$script = $this->guessBootScriptFromWebDir($app, $env))
      {
        throw new dmException(sprintf('Diem can not guess %s app url', $app));
      }

      $appUrl = $this->requestContext['absolute_url_root'].'/'.$script;
    }

    return $appUrl;
  }
  
  protected function guessBootScriptFromWebDir($app, $env)
  {
    $script = false;
    $webDir = sfConfig::get('sf_web_dir');
    
    if(file_exists(dmOs::join($webDir, $app.'_'.$env.'.php')))
    {
      $script = $app.'_'.$env.'.php';
    }
    elseif('front' === $app)
    {
      if (file_exists(dmOs::join($webDir, 'index.php')))
      {
        $script = 'index.php';
      }
      if ('prod' !== $env && file_exists(dmOs::join($webDir, $env.'.php')))
      {
        $script = $env.'.php';
      }
    }
    elseif(file_exists(dmOs::join($webDir, $app.'.php')))
    {
      $script = $app.'.php';
    }

    return $script;
  }
}