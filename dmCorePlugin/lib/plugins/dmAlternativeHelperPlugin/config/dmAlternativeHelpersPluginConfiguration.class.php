<?php

class dmCoreTranslatorPluginConfiguration extends sfPluginConfiguration
{
  protected
  $erviceContainer;
  
  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {
    //$this->dispatcher->connect('dm.context.loaded', array($this, 'listenToContextLoadedEvent'));
  }
  
  /*public function listenToContextLoadedEvent(sfEvent $e)
  {
    $serviceContainer = $e->getSubject()->getServiceContainer();
    
    if ($serviceContainer->hasService('translator_handler'))
    {
      $serviceContainer->getService('translator_handler')->connect();
    }
  }*/
}