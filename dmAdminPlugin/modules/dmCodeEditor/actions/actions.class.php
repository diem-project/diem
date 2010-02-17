<?php

class dmCodeEditorActions extends dmAdminBaseActions
{

  public function executeIndex(sfWebRequest $request)
  {
    sfConfig::set('dm_pageBar_enabled', false);
    sfConfig::set('dm_mediaBar_enabled', false);
    
    $this->editor = $this->getService('code_editor');
  }
  
  public function executeGetDirContent(sfWebRequest $request)
  {
    return $this->renderJson($this->getService('code_editor')->getDirContent($request->getParameter('dir')));
  }
  
  public function executeOpenFile(sfWebRequest $request)
  {
    $this->file = $this->getService('code_editor')->getFileAsArray($request->getParameter('id'));

    if($this->file['is_image'])
    {
      $this->file['media'] = $this->getHelper()->media($this->file['full_path']);
    }

    return $this->file['is_image'] ? 'Image' : 'Code';
  }

  public function executeSaveFile(dmWebRequest $request)
  {
    try
    {
      // disable warnings because they break json response
      @$this->getService('code_editor')->saveFile(
        $request->getParameter('file'),
        $request->getParameter('code')
      );
    }
    catch(Exception $e)
    {
      return $this->renderJson(array(
        'type'    => 'error',
        'message' => 'Save failed : '.$e->getMessage()
      ));
    }

    return $this->renderJson(array(
      'type'    => 'ok',
      'message' => $this->getI18n()->__('Your modifications have been saved')
    ));
  }

}
