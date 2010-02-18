<?php

class dmCodeEditorActions extends dmFrontBaseActions
{

  public function executeLaunch(dmWebRequest $request)
  {
    $this->fileMenu = $this->getService('front_code_editor_file_menu')->build();

    return $this->renderAsync(array(
      'html'  => $this->getPartial('dmCodeEditor/launch'),
      'js'    => array('lib.ui-tabs', 'lib.hotkeys', 'core.codeArea', 'front.codeEditor'),
      'css'   => array('lib.ui-tabs', 'front.codeEditor')
    ), true);
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

    $this->message = $this->isWritable ? '' : $this->getI18n()->__('This file is not writable');
    
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

    try
    {
      @$this->getService('file_backup')->save($file);
    }
    catch(dmException $e)
    {
      return $this->renderJson(array(
        'type' => 'error',
        'message' => 'backup failed : '.$e->getMessage()
      ));
    }

    @file_put_contents($file, $request->getParameter('code'));

    if (dmOs::getFileExtension($file, false) == 'css')
    {
      $return = array(
        'type' => 'css',
        'path' => $this->getHelper()->getStylesheetWebPath(dmOs::getFileWithoutExtension($file))
      );
    }
    else
    {
      $this->getService('cache_cleaner')->clearTemplate();
      
      $return = array(
        'type' => 'php',
        'widgets' => $this->getWidgetInnersForFile($file)
      );
    }

    $return['message'] = $this->getI18n()->__('Your modifications have been saved');
    
    return $this->renderJson($return);
  }

  protected function getWidgetInnersForFile($file)
  {
    /*
     * Find widgets affected by this php file
     */
    $module = preg_replace('|^/([^/]+)/.+|', '$1', str_replace(dmOs::normalize(sfConfig::get('sf_app_module_dir')), '', $file));

    $widgets = array();
    
    foreach($this->getService('page_helper')->getAreas() as $areaArray)
    {
      foreach($areaArray['Zones'] as $zoneArray)
      {
        foreach($zoneArray['Widgets'] as $widgetArray)
        {
          if($widgetArray['module'] === $module)
          {
            ob_start();

            $widgets[$widgetArray['id']] = $this->getService('page_helper')->renderWidgetInner($widgetArray);

            // include debugging output
            if( $output = ob_get_clean())
            {
              $widgets[$widgetArray['id']] = $output.$widgets[$widgetArray['id']];
            }
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

}