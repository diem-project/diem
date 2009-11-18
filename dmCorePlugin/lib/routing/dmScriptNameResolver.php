<?php

class dmScriptNameResolver
{
  protected
  $context;
  
  public function __construct(dmContext $context)
  {
    $this->context = $context;
  }
  
  public function get($app = null, $env = null, $culture = null)
  {
    $app = null === $app ? sfConfig::get('sf_app') : $app;
    $env = null === $env ? sfConfig::get('sf_environment') : $env;
    $culture = null === $culture ? $this->context->getUser()->getCulture() : $culture;

    $knownAppUrls = json_decode(dmConfig::get('base_urls', '[]'), true);

    $appUrlKey = implode('-', array($app, $env, $culture));

    if (!($appUrl = dmArray::get($knownAppUrls, $appUrlKey)))
    {
      if (!$script = $this->guessBootScriptFromWebDir($app, $env))
      {
        throw new dmException(sprintf('Diem can not guess %s app url', $app));
      }

      $appUrl = $this->context->getRequest()->getAbsoluteUrlRoot().'/'.$script;
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