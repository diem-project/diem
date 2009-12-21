<?php

abstract class PluginDmMediaFolder extends BaseDmMediaFolder
{

  /*
   * Getter methods
   */
  public function getName()
  {
    $relPath = $this->get('rel_path');
    
    if(strpos($relPath, '/'))
    {
      $name = basename($relPath);
    }
    elseif($relPath)
    {
      $name = $relPath;
    }
    else
    {
      $name = 'root';
    }
    
    return $name; 
  }

  public function getFullPath()
  {
    return dmOs::join(sfConfig::get('sf_upload_dir'), $this->get('rel_path'));
  }

  public function getNbElements()
  {
    if($this->hasCache('nbElements'))
    {
      return $this->getCache('nbElements');
    }

    $nbMedias = dmDb::query('DmMedia m')
    ->where('m.dm_media_folder_id = ?', $this->get('id'))
    ->count();

    return $this->setCache('nbElements', $nbMedias + $this->getNode()->getNumberChildren());
  }

  public function getSubFoldersByName()
  {
    $foldersByName = array();

    if ($children = $this->getNode()->getChildren())
    {
      foreach ($children as $folder)
      {
        $foldersByName[$folder->getName()] = $folder;
      }
    }

    return $foldersByName;
  }

  public function getDmMediasByFileName()
  {
    $filesName = array();
    foreach ($this->getMedias() as $file)
    {
      $filesName[$file->get('file')] = $file;
    }

    return $filesName;
  }

  public function getMedias()
  {
    if ($this->hasCache('medias'))
    {
      return $this->getCache('medias');
    }

    $medias = $this->_get('Medias');

    foreach($medias as $media)
    {
      $media->set('Folder', $this, false);
    }

    return $this->setCache('medias', $medias);
  }

  /*
   * Check methods
   */

  /**
   * Folder physically exists
   *
   * @return bool
   */
  public function dirExists()
  {
    return is_dir($this->getFullPath());
  }

  public function isWritable()
  {
    return is_writable($this->getFullPath());
  }

  /**
   * Checks if a name already exists in the list of subfolders to a folder
   *
   * @param string $name A folder name
   * @return bool
   */
  public function hasSubFolder($name)
  {
    return dmDb::query('DmMediaFolder f')
    ->where('f.rel_path = ?', trim($this->get('rel_path').'/'.$name, '/'))
    ->andWhere('f.lft > ?', $this->get('lft'))
    ->andWhere('f.rgt < ?', $this->get('rgt'))
    ->exists();
  }

  /*
   * Shortcut to ->getNode()->isRoot()
   */
  public function isRoot()
  {
    return $this->getNode()->isRoot();
  }

  /*
   * Setter methods
   */

  public function setRelPath($v)
  {
    return $this->_set('rel_path', trim($v, '/'));
  }

  /*
   * Manipulation methods
   */

  /**
   * Physically creates folder
   *
   * @return bool succes
   */
  public function create()
  {
    return self::$serviceContainer->getService('filesystem')->mkdir($this->getFullPath());
  }

  /**
   * Change folder name
   *
   * @param string $name
   */
  public function rename($name)
  {
    if ($name === $this->name)
    {
      return;
    }
    
    if ($this->getNode()->isRoot())
    {
      throw new dmException('The root folder cannot be renamed');
    }
    
    if(!$this->isWritable())
    {
      throw new dmException(sprintf('The folder %s is not writable.', dmProject::unRootify($this->fullPath)));
    }
    
    if(dmOs::sanitizeDirName($name) !== $name)
    {
      throw new dmException(sprintf('The target folder: "%s" contains incorrect characters.', $name));
    }
    
    if($this->getNode()->getParent()->hasSubFolder($name))
    {
      throw new dmException(sprintf('The parent folder already contains a folder named "%s".', $name));
    }
    
    $oldName    = $this->getName();
    $oldRelPath = $this->get('rel_path');
    $newRelPath = $this->getNode()->getParent()->get('rel_path').'/'.$name;

    $fs = self::$serviceContainer->getService('filesystem');

    $oldFullPath = $this->getFullPath();
    $newFullPath = dmOs::join($this->getNode()->getParent()->getFullPath(), $name);
    
    if(!rename($oldFullPath, $newFullPath))
    {
      throw new dmException('Can not move %s to %s', dmProject::unRootify($oldFullPath), dmProject::unRootify($newFullPath));
    }

    $this->set('rel_path', $newRelPath);

    $this->clearCache()->save();
    
    //update descendants
    if($descendants = $this->getNode()->getDescendants())
    {
      foreach($descendants as $folder)
      {
        $folder->set('rel_path', dmString::str_replace_once($oldRelPath, $newRelPath, $folder->get('rel_path')));
        $folder->save();
      }
    }
  }

  /**
   * Move into another folder
   *
   * @param DmMediaFolder $folder
   */
  public function move(DmMediaFolder $folder)
  {
    if($folder->getNode()->isDescendantOfOrEqualTo($this))
    {
      throw new dmException('Can not move to a descendant');
    }
    
    if ($this->getNode()->isRoot())
    {
      throw new dmException('The root folder cannot be moved');
    }
    
    if(!$this->isWritable())
    {
      throw new dmException(sprintf('The folder %s is not writable.', dmProject::unRootify($this->fullPath)));
    }
    
    if(!$folder->isWritable())
    {
      throw new dmException(sprintf('The folder %s is not writable.', dmProject::unRootify($folder->fullPath)));
    }
    
    if($folder->hasSubFolder($this->name))
    {
      throw new dmException(sprintf('The selected folder already contains a folder named "%s".', $this->name));
    }
    
    $oldRelPath = $this->get('rel_path');
    $newRelPath = $folder->get('rel_path').'/'.$this->name;

    $fs = self::$serviceContainer->getService('filesystem');

    $oldFullPath = $this->getFullPath();
    $newFullPath = dmOs::join($folder->getFullPath(), $this->name);
    
    if(!rename($oldFullPath, $newFullPath))
    {
      throw new dmException('Can not move %s to %s', dmProject::unRootify($oldFullPath), dmProject::unRootify($newFullPath));
    }

    $this->set('rel_path', $newRelPath);

    $this->clearCache();
    
    $this->getNode()->moveAsFirstChildOf($folder);
    
    //update descendants
    if($descendants = $this->getNode()->getDescendants())
    {
      foreach($descendants as $folder)
      {
        $folder->set('rel_path', dmString::str_replace_once($oldRelPath, $newRelPath, $folder->get('rel_path')));
        $folder->save();
      }
    }
  }

  public function sync()
  {
    $timer = dmDebug::timerOrNull('DmMediaFolder::sync');

    /*
     * Clear php filesystem cache
     * This will avoid some problems
     */
    clearstatcache();

    $this->refresh(true);

    $files = sfFinder::type('file')->maxdepth(0)->ignore_version_control()->in($this->getFullPath());
    $medias = $this->getDmMediasByFileName();

    foreach($files as $file)
    {
      /*
       * Sanitize files name ( move files )
       */
      if (basename($file) != dmOs::sanitizeFileName(basename($file)))
      {
        $renamed_file = dmOs::join(dirname($file), dmOs::sanitizeFileName(basename($file)));
        while(file_exists($renamed_file))
        {
          $renamed_file = dmOs::randomizeFileName($renamed_file);
        }
        rename($file, $renamed_file);
        $file = $renamed_file;
      }

      if (!array_key_exists(basename($file), $medias))
      {
        try
        {
          // File exists, asset does not exist: create asset
          dmDb::create('DmMedia', array(
            'dm_media_folder_id' => $this->get('id'),
            'file' => basename($file)
          ))->save();
        }
        catch(Exception $e)
        {
          dmDebug::kill($this, $medias, $file);
        }
      }
      else
      {
        // File exists, asset exists: do nothing
        unset($medias[basename($file)]);
      }
    }

    foreach ($medias as $name => $media)
    {
      // File does not exist, asset exists: delete asset
      $media->delete();
    }

    $dirs = sfFinder::type('dir')->maxdepth(0)->discard(".*")->ignore_version_control()->in($this->getFullPath());
    $folders = $this->getSubfoldersByName();

    foreach($dirs as $dir)
    {
      $dirName = basename($dir);
      /*
       * Sanitize folders name ( move folders )
       */
      if ($dirName != dmOs::sanitizeDirName($dirName))
      {
        $renamedDir = dmOs::join(dirname($dir), dmOs::sanitizeDirName($dirName));
        while(dir_exists($renamedDir))
        {
          $renamedDir = dmOs::randomizeDirName($renamedDir);
        }
        rename($dir, $renamedDir);
        $dir = $renamedDir;
        $dirName = basename($dir);
      }

      /*
       * Exists in fs, not in db
       */
      if (!array_key_exists($dirName, $folders))
      {
        $subfolderRelPath = trim(dmOs::join($this->get('rel_path'), $dirName), '/');

        if ($folder = $this->getTable()->findOneByRelPath($subfolderRelPath))
        {
          // folder exists in db but is not linked to its parent !
          $folder->getNode()->moveAsLastChildOf($this);
        }
        else
        {
          // dir exists in filesystem, not in database: create folder in database
          $folder = dmDb::create('DmMediaFolder', array(
            'rel_path' => $subfolderRelPath
          ));

          $folder->getNode()->insertAsLastChildOf($this);
        }
      }
      else
      {
        // dir exists in filesystem and database: do nothing
//        dmDebug::show('ok : '.$dirName, $dir, is_dir($dir), array_keys($folders));
        $folder = $folders[$dirName];
        unset($folders[$dirName]);
      }

      $folder->sync();
    }

    /*
     * Not unsetted folders
     * don't exist in fs
     */
    foreach ($folders as $folder)
    {
      $folder->getNode()->delete();
    }
    
    $this->refresh();
    $this->refreshRelated('Medias');

    $timer && $timer->addTime();
  }

  /*
   * Same as getNode()->getParent()->id
   * but will not hydrate full parent
   */
  public function getNodeParentId()
  {
    if (!$this->get('lft'))
    {
      return null;
    }

    $stmt = Doctrine_Manager::connection()->prepare('SELECT p.id
FROM dm_media_folder p
WHERE p.lft < ? AND p.rgt > ?
ORDER BY p.rgt ASC
LIMIT 1')->getStatement();

    $stmt->execute(array($this->get('lft'), $this->get('rgt')));
    
    $result = $stmt->fetch(PDO::FETCH_NUM);
    
    return $result[0];
  }

  /*
   * Common methods
   */

  public function __toString()
  {
    return $this->get('rel_path').' ('.$this->get('id').')';
  }

  /*
   * Override methods
   */
  public function save(Doctrine_Connection $conn = null)
  {
    // physical existence
    if (!$this->dirExists() && !$this->getNode()->isRoot())
    {
      if (!$this->create())
      {
        throw new dmException(sprintf('Impossible to create folder "%s"', $this->getFullPath()));
      }
    }

    return parent::save($conn);
  }

  public function delete(Doctrine_Connection $conn = null)
  {
    // Remove dir itself
    if(!$this->getNode()->isRoot() && $this->dirExists())
    {
      self::$serviceContainer->getService('filesystem')->deleteDir($this->fullPath);
    }

    return parent::delete($conn);
  }

}