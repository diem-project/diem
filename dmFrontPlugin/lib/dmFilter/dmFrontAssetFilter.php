<?php

class dmFrontAssetFilter extends dmAssetFilter
{
	protected function getJs()
	{
    if ($this->userCanEdit())
    {
    	$jsArray = array(
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
	  else
	  {
	  	$jsArray = array(
        'lib.metadata',
        'core.config',
        'core.plugins',
        'core.ctrl',
        'front.config',
        'front.ctrl'
	  	);
	  }
    
    return array_merge(parent::getJs(), $jsArray);
	}

	protected function getCss()
	{
		if ($this->userCanEdit())
		{
      $cssArray = array(
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
		else
		{
      $cssArray = array(
        'core.util'
      );
		}
		
		return array_merge(parent::getCss(), $cssArray);
	}

	protected function userCanEdit()
	{
		return dm::getUser()->can('tool_bar_front');
	}
	
}