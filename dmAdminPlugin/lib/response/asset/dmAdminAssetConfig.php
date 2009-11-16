<?php

class dmAdminAssetConfig extends dmAssetConfig
{
  protected function _getStylesheets()
  {
    return array(
      'lib.reset',
      sfConfig::get('sf_web_debug') ? 'lib.symfony_debug' : null,
      'lib.ui',
      'core.util',
      'core.editMode',
      'core.sprites',
      'core.sprite16',
      'core.sprite24',
      'core.toolBar',
      'core.pageBar',
      'core.mediaBar',
      'core.form',
      'admin.main',
      'admin.layout',
      'admin.bars',
      'admin.flash',
      'admin.generator',
      'admin.breadCrumb'
    );
  }
  
  protected function _getJavascripts()
  {
    return array(
      'lib.jquery',
      sfConfig::get('sf_web_debug') ? 'lib.symfony_debug' : null,
      'lib.metadata',
      'lib.cookie',
      'lib.ui-core',
      'lib.blockUI',
      'lib.hotkeys',
      'core.config',
      'core.plugins',
      'core.editPlugins',
      'core.ctrl',
      'core.editCtrl',
      'core.toolBar',
      'core.pageBar',
      'core.mediaBar',
      'admin.ctrl',
      'admin.toolBar',
      'admin.pageBar',
      'admin.mediaBar'
    );
  }
}