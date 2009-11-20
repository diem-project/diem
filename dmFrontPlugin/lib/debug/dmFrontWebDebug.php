<?php

class dmFrontWebDebug extends dmWebDebug
{
  /**
   * Configures the web debug toolbar.
   */
  public function configure()
  {
    if (sfConfig::get('sf_debug') && sfConfig::get('sf_cache'))
    {
      $this->setPanel('cache', new sfWebDebugPanelCache($this));
    }
    
    if (sfConfig::get('sf_logging_enabled'))
    {
      $this->setPanel('config', new dmWebDebugPanelConfig($this));
      $this->setPanel('view', new sfWebDebugPanelView($this));
    }
    
    $this->setPanel('logs', new sfWebDebugPanelLogs($this));

    if (sfConfig::get('sf_debug'))
    {
      $this->setPanel('time', new sfWebDebugPanelTimer($this));
    }
    
    $this->setPanel('memory', new dmWebDebugPanelMemory($this));

    $this->setPanel('mailer', new sfWebDebugPanelMailer($this));
  }
}