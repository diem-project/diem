<?php

abstract class dmBaseLinkTagFactory
{
  protected
  $serviceContainer;
  
  public function __construct(dmBaseServiceContainer $serviceContainer)
  {
    $this->serviceContainer = $serviceContainer;
  }
  
  abstract public function buildLink($source);
}