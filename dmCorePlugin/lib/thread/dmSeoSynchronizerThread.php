<?php

class dmSeoSynchronizerThread extends dmThread
{
  public function doExecute()
  {
    $seoSynchronizer = new $this->options['class']($this->getModuleManager(), $this->options['culture']);
    
    $seoSynchronizer->execute($this->options['modules']);
  }
  
}