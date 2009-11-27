<?php
/**
 * dmCodeEditor actions.
 *
 * @package    diem
 * @subpackage dmCodeEditor
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class dmCodeEditorActions extends dmAdminBaseActions
{

  public function executeIndex(sfWebRequest $request)
  {
    $treeJson = array(
      array(
        'attributes' => array(
          'id' => dmCodeEditorTools::encodeUrlTree('/')
        ),
        'state' => 'open',
        'data' => array(
          'title' => '/',
          'attributes' => array(
            'class' => 'dir root'
          )
        ),
        'children' => $this->getTreeJson(sfCOnfig::get('sf_root_dir'))
      )
    );
    
    $this->getUser()->logAlert('The admin code editor is <strong>-NOT-</strong> completed yet an may not work', false);
    
    $this->getResponse()->addJavascriptConfig('dm_tree_json', $treeJson);
  }
  
  public function executeConstructTree(sfWebRequest $request)
  {
    $id = $request->getParameter("id", dmCodeEditorTools::encodeUrlTree('/'));
    
    $currentDir = dmProject::rootify(dmCodeEditorTools::decodeUrlTree($id));
    
    $this->getResponse()->setContentType('application/json');

    return $this->renderText(json_encode($this->getTreeJson($currentDir)));
  }
  
  protected function getTreeJson($currentDir)
  {
    $data = array();
    
    $arrayDir = sfFinder::type('dir')->maxdepth(0)->not_name("/^\..*/")->follow_link()->in($currentDir);
      natcasesort($arrayDir);

      $arrayFile = sfFinder::type('file')->maxdepth(0)->not_name("/^\..*/")->follow_link()->in($currentDir);
      natcasesort($arrayFile);
    
      foreach( $arrayDir as $dir )
      {
        $isReadable = is_readable($dir);
        $isWritable = is_writable($dir);
        
        $data[] = array(
          'attributes' => array(
            'id' => dmCodeEditorTools::encodeUrlTree($dir)
          ),
          'state' => $isReadable ? 'closed' : '',
          'data' => array(
            'title' => basename($dir),
            'attributes' => array(
              'class' => sprintf('dir %s %s',
                 $isReadable ? 'readable_dir' : 'not_readable_dir',
                 $isWritable ? 'writable_dir' : 'not_writable_dir'
              )
            )
          )
        );
      }
      
      foreach( $arrayFile as $file )
      {
        $data[] = array(
          'attributes' => array(
            'id' => dmCodeEditorTools::encodeUrlTree($file)
          ),
          'state' => '',
          'data' => array(
            'title' => basename($file),
            'attributes' => array(
              'class' => sprintf('file file_%s%s%s',
                 strtolower(dmOs::getFileExtension($file, false)),
                 is_readable($file) ? ' readable_file' : '',
                 is_writable($file) ? ' writable_file' : ''
              )
            )
          )
        );
      }
    
    return $data;
  }
  
  public function executeOpenFile(sfWebRequest $request)
  {
    //dernier fichier ouvert aze::getUser()->addFileOpened($relPath);
    
    $id = $request->getParameter("id");
    $relPath = dmCodeEditorTools::decodeUrlTree($id);
    
    $this->file = dmProject::rootify($relPath);
    if (!is_readable($this->file) || !is_file($this->file))
    {
      return $this->renderText('[KO] | '.$relPath.' does not exist or is not readable');
    }
    
    $type = dmOs::getFileMime($this->file);
    $this->isImage = strpos($type, 'image') === 0;
    $this->code = dmString::unixify(file_get_contents($this->file));
    $this->isWritable = is_writable($this->file) && strpos($this->file, dmProject::rootify('data/backup')) !== 0;
    
    $this->path = dmProject::unRootify($this->file);
    
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
      $this->context->get('file_backup')->save($file);
    }
    catch(dmException $e)
    {
      return $this->renderText(json_encode(array(
        'type' => 'error',
        'message' => 'backup failed : '.$e->getMessage()
      )));
    }

    file_put_contents($file, $request->getParameter('code'));

    return $this->renderText(json_encode(array(
      'type' => 'ok',
      'message' => dm::getI18n()->__('Your modifications have been saved')
    )));
  }
  
  public function executeCopyCut(sfWebRequest $request)
  {
    $id = $request->getParameter("id");
    $this->rootDir = sfConfig::get('sf_root_dir');
    $decodeCopyCut = dmCodeEditorTools::decodeUrlTree($id);
    $CopyCut = $this->rootDir.$decodeCopyCut;
    if($CopyCut == $this->rootDir)
    {
      return $this->renderText('[KO] | This is the root dir, you don\'t have permission');
    }
    if(strpos($CopyCut, $this->rootDir) === 0)
    {
      if(file_exists($CopyCut) && is_readable($CopyCut))
      {
        $this->getUser()->setAttribute('code_editor_file_copy_cut', $CopyCut);
        $this->getUser()->setAttribute('code_editor_is_cut', $request->getParameter('is_cut'));
        return $this->renderText('[ok]');
      }
      else
      {
        return $this->renderText('[KO] | '.$decodeCopyCut.' does not exist or is not readable');
      }
    }
    return $this->renderText('[KO] | '.$decodeCopyCut.' does not exist in /');
  }
  
  public function executePaste(sfWebRequest $request)
  {
    $filesystem = $this->context->getFilesystem();
    
    $id = $request->getParameter("id");
    $this->rootDir = sfConfig::get('sf_root_dir');
    $decodePasteDir = dmCodeEditorTools::decodeUrlTree($id);
    $pasteDir = $this->rootDir.$decodePasteDir;
    if(strpos($pasteDir, $this->rootDir) === 0)
    {
      if(file_exists($pasteDir) && is_writable($pasteDir))
      {
        
        if($copyDir = $this->getUser()->getAttribute('code_editor_file_copy_cut'))
        {
          if(!$this->context->getFilesystem()->copyRecursive(dmCodeEditorTools::decodeUrlTreeForCopy($copyDir),dmCodeEditorTools::decodeUrlTreeForCopy($pasteDir)))
          {
            return $this->renderText('[KO] | An error occurred ');
          }
          if($this->getUser()->getAttribute('code_editor_is_cut') == 'cut')
          {
            if(!$this->context->getFilesystem()->unlink($copyDir))
            {
              return $this->renderText('[KO] | An error occurred while deleting the file or dir : '.$copyDir);
            }
          }
          
          $this->getUser()->setAttribute('code_editor_file_copy_cut', null);
          $this->getUser()->setAttribute('code_editor_is_cut', null);
          return $this->renderText('[OK] |'.dmCodeEditorTools::encodeUrlTree($pasteDir.'/'.basename($copyDir)));
        }
        else
        {
          return $this->renderText('[KO] | There are no files or dir to paste');
        }
      }
      else
      {
        return $this->renderText('[KO] | '.$decodePasteDir.' does not exist or is not writable');
      }
    }
    return $this->renderText('[KO] | '.$decodePasteDir.' does not exist in /');
  }
  
  public function executeDelete(sfWebRequest $request)
  {
    $id = $request->getParameter("id");
    $this->rootDir = sfConfig::get('sf_root_dir');
    $decodeDeleteDir = dmCodeEditorTools::decodeUrlTree($id);
    $deleteDir = $this->rootDir.$decodeDeleteDir;
    if($deleteDir == $this->rootDir)
    {
      return $this->renderText('[KO] | This is the root dir, you don\'t have permission');
    }
    if(strpos($deleteDir, $this->rootDir) === 0)
    {
      if(file_exists($deleteDir) && is_writable($deleteDir))
      {
        if($this->context->getFilesystem()->unlink($deleteDir))
        {
          return $this->renderText('[OK] | Successfully deleted');
        }
        else
        {
          return $this->renderText('[KO] | An error occurred while deleting the file : '.$decodeDeleteDir);
        }
      }
      else
      {
        return $this->renderText('[KO] | '.$decodeDeleteDir.' does not exist or is not writable');
      }
    }
    return $this->renderText('[KO] | '.$decodeDeleteDir.' does not exist in /');
  }

  public function executeRenameOrCreate(sfWebRequest $request)
  {
    $id = $request->getParameter("id");
    $data = $request->getParameter("data");
    $this->rootDir = sfConfig::get('sf_root_dir');
    $oldName = $this->rootDir.dmCodeEditorTools::decodeUrlTree($id);
    
    if($oldName == $this->rootDir)
    {
      return $this->renderText('[KO] | This is the root dir, you don\'t have permission');
    }
    if(strpos($oldName, $this->rootDir) === 0)
    {
      $newName =  str_replace(basename($oldName), $data, $oldName);
      if(file_exists($oldName))
      {
        if(file_exists($newName))
        {
          return $this->renderText('[KO] | This file or dir already exists');
        }
        
        if(rename($oldName, $newName))
        {
          return $this->renderText('[OK] | Succefully renamed '.basename($oldName).' => '.basename());
        }
        else
        {
          return $this->renderText('[KO] | | An error occurred while rename '.basename($oldName).' => '.basename());
        }
      }
      else
      {
        if($request->getParameter('create') == 'file')
        {
          if($this->context->getFilesystem()->touch($newName))
          {
            return $this->renderText('[OK] | Succefully created new file : '.basename($newName));
          }
          else
          {
            return $this->renderText('[KO] | An error occurred while creating the file : '.basename($newName));
          }
        }
        elseif($request->getParameter('create') == 'dir')
        {
          if(mkdir($newName))
          {
            return $this->renderText('[OK] | Succefully created new dir : '.basename($newName));
          }
          else
          {
            return $this->renderText('[KO] | An error occurred while creating the dir : '.basename($newName));
          }
        }
      }
    }
    else
    {
      return $this->renderText('[KO] | This file or dir does not exist in /');
    }
  }

}
