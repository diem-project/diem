<?php

class dmAdminAssetFilter extends dmAssetFilter
{

	protected function getJs()
	{
		return array_merge(parent::getJs(), array(
        'lib.metadata',
//        'lib.ajaxqueue',
        'lib.cookie',
        'lib.ui-admin',
//        $this->jQueryUiI8n(),
		    'lib.blockUI',
//        'lib.jgrowl',
//		    'lib.form',
        'lib.hotkeys',
        'core.config',
        'core.plugins',
        'core.editPlugins',
        'core.ctrl',
        'core.editCtrl',
//        'core.form',
        'core.toolBar',
        'core.pageBar',
        'core.mediaBar',
        'admin.config',
        'admin.ctrl',
//        'admin.form',
        'admin.toolBar',
        'admin.pageBar',
        'admin.mediaBar'
    ));
	}

	protected function getCss()
	{
    return array_merge(parent::getCss(), array(
        'lib.ui',
//        'lib.jgrowl',
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
//        'admin.breadCrumb',
        'admin.flash',
        'admin.module',
        'admin.generator'
    ));
	}


}