<?php

class dmAlternativeHelperPluginConfiguration extends sfPluginConfiguration
{
  
  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {
    $this->enableHelper();
  }

  protected function enableHelper()
  {
    sfConfig::set('sf_standard_helpers', array_unique(array_merge(sfConfig::get('sf_standard_helpers', array()), array('DmAlternative'))));
  }

}