<?php

class dmFactoryConfigHandler extends sfFactoryConfigHandler
{
  
  public function execute($configFiles)
  {
    $code = parent::execute($configFiles);
    
    $code = str_replace('Swift::registerAutoload();', '// Swift::registerAutoload();', $code);
    $code = str_replace('sfMailer::initialize();', '// sfMailer::initialize();', $code);
  
    return $code;
  }

}