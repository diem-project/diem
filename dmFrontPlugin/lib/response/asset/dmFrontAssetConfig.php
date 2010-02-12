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
        'front.dmReset',
        'lib.ui',
        'lib.ui-dialog',
        'core.tool',
        'core.editMode',
        'core.sprites',
        'core.sprite16',
        'core.sprite24',
        'core.toolBar',
        'core.pageBar',
        'core.mediaBar',
        'core.form',
        'front.base',
        'front.toolBar',
        'front.zone',
        'front.widget',
        'front.form'
      );
    }
    
    return array(
      sfConfig::get('sf_web_debug') ? 'lib.symfony_debug' : null,
      'core.tool',
      'front.base'
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
        'lib.ui-core',
        'lib.ui-widget',
        'lib.ui-mouse',
        'lib.ui-position',
        'lib.ui-draggable',
        'lib.ui-droppable',
        'lib.ui-sortable',
        'lib.ui-dialog',
        'lib.blockUI',
        'lib.form',
        'lib.jstree',
        'core.config',
        'core.plugins',
        'core.editPlugins',
        'core.ctrl',
        'core.editCtrl',
        'core.toolBar',
        'core.pageBar',
        'core.mediaBar',
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
      'front.ctrl'
    );
  }

  public function userCanEdit()
  {
    return $this->user->can('tool_bar_front');
  }
}