<?php

class dmScriptNameResolver
{
  protected
  $context;
  
  public function __construct(sfContext $context)
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
    if(file_exists(dmOs::join(sfConfig::get('sf_web_dir'), $app.'_'.$env.'.php')))
    {
      $script = $app.'_'.$env.'.php';
    }
    elseif(file_exists(dmOs::join(sfConfig::get('sf_web_dir'), $app.'.php')))
    {
      $script = $app.'.php';
    }
    elseif($app == 'front')
    {
      $script = $env == 'prod' ? 'index.php' : $env.'.php';
    }
    else
    {
      $script = false;
    }
    
    return $script;
  }
}