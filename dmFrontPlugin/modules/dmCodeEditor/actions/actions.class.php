<?php

class dmCodeEditorActions extends dmFrontBaseActions
{

	public function executeLaunch(dmWebRequest $request)
	{
		$this->fileMenu = new dmHtmlMenu($this->getFileMenu());

		$this->js =
		file_get_contents(dmOs::join(sfConfig::get('sf_web_dir'), sfConfig::get('dm_core_asset'), 'lib/jquery-ui/js/ui.tabs.min.js')).
		dmJsMinifier::transform(
		file_get_contents(dmOs::join(sfConfig::get('sf_web_dir'), sfConfig::get('dm_core_asset'), 'js/dmCoreCodeArea.js')).
		file_get_contents(dmOs::join(sfConfig::get('sf_web_dir'), sfConfig::get('dm_front_asset'), 'js/dmFrontCodeEditor.js'))
		)
		;

		$this->css = dmCssMinifier::transform(
		file_get_contents(dmOs::join(sfConfig::get('sf_web_dir'), sfConfig::get('dm_front_asset'), 'css/codeEditor.css'))
		);
	}

	public function executeFile(dmWebRequest $request)
	{
		$this->forward404Unless(
		file_exists($this->file = dmOs::join(sfConfig::get('sf_root_dir'), $request->getParameter('file'))),
		$request->getParameter('file').' does not exists'
		);

		$this->code = file_get_contents($this->file);
		$this->path = dmProject::unRootify($this->file);
		$this->isWritable = is_writable($this->file);

		$this->message = $this->isWritable ? '' : dm::getI18n()->__("This file is not writable");
		
		$this->textareaOptions = array(
		  'spellcheck' => 'false'
		);
		
		if(!$this->isWritable)
		{
			$this->textareaOptions['readonly'] = 'true';
		}
	}

	public function executeSave(dmWebRequest $request)
	{
		$file = dmProject::rootify($request->getParameter('file'));

		$this->forward404Unless(
		file_exists($file),
		$file.' does not exists'
		);

    $this->getResponse()->setContentType('application/json');
		
		try
		{
			$this->getDmContext()->getFileBackup()->save($file);
		}
		catch(dmException $e)
		{
      return $this->renderText(json_encode(array(
        'type' => 'error',
        'message' => 'backup failed : '.$e->getMessage()
      )));
		}

		file_put_contents($file, $request->getParameter('code'));

		if (dmOs::getFileExtension($file, false) == 'css')
		{
			$return = array(
    	  'type' => 'css',
    	  'path' => str_replace(sfConfig::get('sf_web_dir'), '', $file)
			);
		}
		else
		{
			$return = array(
    	  'type' => 'php',
    	  'widgets' => $this->getWidgetInnersForFile($file)
			);
		}

		$return['message'] = dm::getI18n()->__('Your modifications have been saved');
		
		return $this->renderText(json_encode($return));
	}

	protected function getWidgetInnersForFile($file)
	{
		/*
		 * Find widgets affected by this php file
		 */
		$module = preg_replace('|^/([^/]+)/.+|', '$1', str_replace(sfConfig::get('sf_app_module_dir'), '', $file));

		$helper = $this->getDmContext()->getPageHelper();
		$widgets = array();
		foreach($helper->getAreas() as $areaArray)
		{
			foreach($areaArray['Zones'] as $zoneArray)
			{
				foreach($zoneArray['Widgets'] as $widgetArray)
				{
					if($widgetArray['module'] == $module)
					{
						$widgets[$widgetArray['id']] = $helper->renderWidgetInner($widgetArray);
					}
				}
			}
		}
		
		return $widgets;
	}

	protected function encodePath($path)
	{
		return str_replace(array('.', '/'), array('_DOT_', '_SLASH_'), $path);
	}

	protected function decodePath($path)
	{
		return str_replace(array('_DOT_', '_SLASH_'), array('.', '/'), $path);
	}

	protected function getFileMenu()
	{

		$moduleDirs = sfFinder::type('dir')->maxDepth(0)->in(sfConfig::get('sf_app_module_dir'));
		natcasesort($moduleDirs);

		$controllers = array();
		$templates = array();
		foreach($moduleDirs as $moduleDir)
		{
			if(count($found = sfFinder::type('file')->in($moduleDir.'/actions')))
			{
				natcasesort($found);
				$files = array();
				foreach($found as $path)
				{
					$files[] = array(
            'name' => basename($path),
            'anchor' => dmProject::unRootify($path)
					);
				}
				$controllers[] = array(
          'name' => basename($moduleDir),
          'menu' => $files
				);
			}

			if(count($found = sfFinder::type('file')->in($moduleDir.'/templates')))
			{
				natcasesort($found);
				$files = array();
				foreach($found as $path)
				{
					$files[] = array(
            'name' => str_replace($moduleDir.'/templates/', '', $path),
            'anchor' => dmProject::unRootify($path)
					);
				}
				$templates[] = array(
          'name' => basename($moduleDir),
          'menu' => $files
				);
			}
		}

		$cssDir = dm::getUser()->getTheme()->getFullPath('css');
		$cssFiles = sfFinder::type('file')->name('*.css')->in($cssDir);
		natcasesort($cssFiles);
		$stylesheets = array();
		foreach($cssFiles as $cssFile)
		{
			if (dmProject::isInProject($cssFile))
			{
				$stylesheets[] = array(
          'name' => str_replace($cssDir.'/', '', $cssFile),
          'anchor' => dmProject::unRootify($cssFile)
				);
			}
		}

		return array(
		array(
        'name' => dm::getI18n()->__('Controllers'),
        'menu' => $controllers
		),
		array(
        'name' => dm::getI18n()->__('Templates'),
        'menu' => $templates
		),
		array(
        'name' => dm::getI18n()->__('Stylesheets'),
        'menu' => array(
		array(
            'name' => dm::getUser()->getTheme()->getName(),
            'menu' => $stylesheets
		)
		)
		),
		);

	}

}