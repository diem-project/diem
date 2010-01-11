<?php

class dmSfBrowser extends sfBrowser
{


  /**
   * Returns the current application context.
   *
   * @param  bool $forceReload  true to force context reload, false otherwise
   *
   * @return sfContext
   */
  public function getContext($forceReload = false)
  {
    if (null === $this->context || $forceReload)
    {
      $isContextEmpty = null === $this->context;
      $context = $isContextEmpty ? sfContext::getInstance() : $this->context;

      // create configuration
      $currentConfiguration = $context->getConfiguration();
      $configuration = ProjectConfiguration::getApplicationConfiguration($currentConfiguration->getApplication(), $currentConfiguration->getEnvironment(), $currentConfiguration->isDebug());

      // connect listeners
      $configuration->getEventDispatcher()->connect('application.throw_exception', array($this, 'listenToException'));
      foreach ($this->listeners as $name => $listener)
      {
        $configuration->getEventDispatcher()->connect($name, $listener);
      }
      
      // create context
      $this->context = dm::createContext($configuration);
      unset($currentConfiguration);

      if (!$isContextEmpty)
      {
        sfConfig::clear();
        sfConfig::add($this->rawConfiguration);
      }
      else
      {
        $this->rawConfiguration = sfConfig::getAll();
      }
    }

    return $this->context;
  }
}