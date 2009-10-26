<?php

class dmPageSynchronizerThread extends dmThread
{
  public function doExecute()
  {
    $pageSynchronizer = new $this->options['class']($this->getModuleManager());
    
    $pageSynchronizer->execute($this->options['modules']);
  }
  
}