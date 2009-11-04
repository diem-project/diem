<?php

abstract class dmAssetConfig
{
  protected
  $dispatcher,
  $user;
  
  public function __construct(sfEventDispatcher $dispatcher, dmCoreUser $user)
  {
    $this->dispatcher = $dispatcher;
    $this->user       = $user;
    
    $this->initialize();
  }
  
  public function initialize()
  {
    
  }
  
  abstract protected function _getStylesheets();
  
  abstract protected function _getJavascripts();
  
  public function getStylesheets()
  {
    return array_unique(array_filter($this->_getStylesheets()));
  }
  
  public function getJavascripts()
  {
    return array_unique(array_filter($this->_getJavascripts()));
  }
}