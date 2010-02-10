<?php

class dmFactoryConfigHandler extends sfFactoryConfigHandler
{
  
  public function execute($configFiles)
  {
    $code = parent::execute($configFiles);

    if(!sfConfig::get('dm_performance_enable_mailer', true))
    {
      $code = str_replace('Swift::registerAutoload();', '// Swift::registerAutoload();', $code);
      $code = str_replace('sfMailer::initialize();', '// sfMailer::initialize();', $code);
    }
  
    return $code;
  }

}