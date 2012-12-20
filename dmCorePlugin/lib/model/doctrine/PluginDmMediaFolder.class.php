<?php

abstract class PluginDmMediaFolder extends BaseDmMediaFolder
{

  /**
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

  public function getNbElements($refresh = false)
  {
    if($this->hasCache('nbElements') && !$refresh)
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

  public function getMedias($refresh = false)
  {
    if ($this->hasCache('medias') && !$refresh)
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

  /**
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

  /**
   * Checks if a file already exists in this folder
   *
   * @param string $name A file name
   * @return bool
   */
  public function hasFile($name)
  {
    return dmDb::query('DmMedia m')
    ->where('m.dm_media_folder_id = ?', $this->get('id'))
    ->andWhere('m.file = ?', $name)
    ->exists();
  }

  /**
   * Shortcut to ->getNode()->isRoot()
   */
  public function isRoot()
  {
    return $this->getNode()->isRoot();
  }

  /**
   * Setter methods
   */

  public function setRelPath($v)
  {
    return $this->_set('rel_path', trim($v, '/'));
  }

  /**
   * Manipulation methods
   */

  /**
   * Physically creates folder
   *
   * @return bool succes
   */
  public function create()
  {
    return $this->getService('filesystem')->mkdir($this->getFullPath());
  }

  /**
   * Change folder name
   *
   * @param string $name
   */
  public function rename($name)
  {
    if ($name === $this->get('name'))
    {
      return $this;
    }
    
    if ($this->getNode()->isRoot())
    {
      throw new dmException('The root folder cannot be renamed');
    }
    
    if(!$this->isWritable())
    {
      throw new dmException(sprintf('The folder %s is not writable.', dmProject::unRootify($this->get('fullPath'))));
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

    $fs = $this->getService('filesystem');

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

    return $this;
  }

  /**
   * Move into another folder
   *
   * @param DmMediaFolder $folder
   */
  public function move(DmMediaFolder $folder)
  {
    if($folder->id == $this->nodeParentId)
    {
      return $this;
    }

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
      throw new dmException(sprintf('The folder %s is not writable.', dmProject::unRootify($this->get('fullPath'))));
    }
    
    if(!$folder->isWritable())
    {
      throw new dmException(sprintf('The folder %s is not writable.', dmProject::unRootify($folder->get('fullPath'))));
    }
    
    if($folder->hasSubFolder($this->name))
    {
      throw new dmException(sprintf('The selected folder already contains a folder named "%s".', $this->get('name')));
    }
    
    $oldRelPath = $this->get('rel_path');
    $newRelPath = $folder->get('rel_path').'/'.$this->get('name');

    $fs = $this->getService('filesystem');

    $oldFullPath = $this->getFullPath();
    $newFullPath = dmOs::join($folder->getFullPath(), $this->get('name'));
    
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

    return $this;
  }

  public function sync($depth = 99)
  {
    return $this->getService('media_synchronizer')->execute($this, $depth);
  }

  /**
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

  /**
   * Common methods
   */

  public function __toString()
  {
    return $this->get('rel_path').' ('.$this->get('id').')';
  }

  /**
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
      $this->getService('filesystem')->deleteDir($this->fullPath);
    }

    return parent::delete($conn);
  }

}