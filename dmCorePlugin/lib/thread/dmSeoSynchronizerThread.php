<?php

class dmSeoSynchronizerThread extends dmThread
{
  public function doExecute()
  {
    $markdown = new $this->options['markdown_class']();
    
    $seoSynchronizer = new $this->options['class']($this->getModuleManager(), $markdown, $this->options['culture']);
    
    $seoSynchronizer->execute($this->options['modules']);
  }
  
}