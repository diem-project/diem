<?php

class dmSeoSynchronizerThread extends dmThread
{
  public function doExecute()
  {
    $seoSynchronizer = new $this->options['class']($this->getModuleManager());
    
    foreach($this->options['cultures'] as $culture)
    {
      $seoSynchronizer->execute($this->options['modules'], $culture);
    }
  }
  
}