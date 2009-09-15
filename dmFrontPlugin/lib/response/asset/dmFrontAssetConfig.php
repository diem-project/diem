<?php

class dmFrontAssetConfig extends dmAssetConfig
{
  protected function _getStylesheets()
  {
    if ($this->userCanEdit())
    {
      return array(
        'lib.reset',
        sfConfig::get('sf_web_debug') ? 'lib.symfony_debug' : null,
        'front.dm_reset',
        'lib.ui',
        'lib.ui-dialog',
        'lib.ui-resizable',
        'lib.ui-tabs',
        'lib.markitup',
        'lib.markitupSet',
        'core.util',
        'core.editMode',
        'core.interface',
        'core.sprites',
        'core.sprite16',
        'core.sprite24',
        'core.toolBar',
        'core.pageBar',
        'core.mediaBar',
        'core.form',
        'front.toolBar',
        'front.zone',
        'front.widget',
        'front.form',
        'front.codeEditor'
      );
    }
    
    return array(
      'lib.reset',
      sfConfig::get('sf_web_debug') ? 'lib.symfony_debug' : null,
      'core.util'
    );
  }
  
  protected function _getJavascripts()
  {
    if ($this->userCanEdit())
    {
      return array(
        'lib.jquery',
        sfConfig::get('sf_web_debug') ? 'lib.symfony_debug' : null,
        'lib.metadata',
        'lib.cookie',
        'lib.ui-front',
        'lib.blockUI',
        'lib.form',
        'lib.hotkeys',
        'lib.markitup',
        'lib.markitupSet',
        'core.config',
        'core.plugins',
        'core.editPlugins',
        'core.ctrl',
        'core.editCtrl',
        'core.form',
        'core.tabForm',
        'core.toolBar',
        'core.pageBar',
        'core.mediaBar',
        'front.config',
        'front.ctrl',
        'front.editCtrl',
        'front.form',
        'front.toolBar',
        'front.pageBar',
        'front.mediaBar',
        'front.page',
        'front.area',
        'front.zone',
        'front.widget',
        'front.widgetForms'
      );
    }
    
    return array(
      'lib.jquery',
      sfConfig::get('sf_web_debug') ? 'lib.symfony_debug' : null,
      'lib.metadata',
      'core.config',
      'core.plugins',
      'core.ctrl',
      'front.config',
      'front.ctrl'
    );
  }

  public function userCanEdit()
  {
    return $this->user->can('tool_bar_front');
  }
}