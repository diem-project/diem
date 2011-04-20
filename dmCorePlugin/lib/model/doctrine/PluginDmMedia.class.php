<?php

abstract class PluginDmMedia extends BaseDmMedia
{
  protected
  $isRefreshed = false;

  public function getTimeHash()
  {
    return $this->checkFileExists() ? substr(md5(filemtime($this->getFullPath())), -5) : null;
  }

  /**
   * Store a copy of the file in backup folder
   */
  public function backup()
  {
    if(!$backupFolder = $this->getBackupFolder())
    {
      throw new dmException(sprintf('Can not create backup folder for %s', $this));
    }

    $this->copyTo(dmDb::create('DmMedia')->set('Folder', $backupFolder))->save();
  }

  public function getBackupFolder()
  {
    return dmDb::table('DmMediaFolder')->findOneByRelPathOrCreate(
      $this->get('Folder')->get('rel_path').'/backup'
    );
  }

  public function getForeigns()
  {
    if ($this->hasCache('foreigns'))
    {
      return $this->getCache('foreigns');
    }

    $foreigns = array();

    foreach($this->getTable()->getRelationHolder()->getForeigns() as $foreignRelation)
    {
      if ($foreign = $relation->fetchRelatedFor($this))
      {
        $foreigns[] = $foreign;
      }
    }

    return $this->setCache('foreigns', $foreigns);
  }

  public function getNbForeigns()
  {
    return count($this->getNbForeigns());
  }

  public function getDimensions()
  {
    if (!$this->isImage() || !$this->checkFileExists())
    {
      return false;
    }

    if (!$dimensions = $this->_get('dimensions'))
    {
      $infos = getimagesize($this->getFullPath());
      $this->_set('dimensions', $dimensions = $infos[0]."x".$infos[1], false)->save();
    }

    return $dimensions;
  }

  public function getWidth()
  {
    if($this->hasCache('width'))
    {
      return $this->getCache('width');
    }
    
    return $this->setCache('width', ($dimensions = $this->get('dimensions')) ? substr($dimensions, 0, strpos($dimensions, 'x')) : null);
  }

  public function getHeight()
  {
    if($this->hasCache('height'))
    {
      return $this->getCache('height');
    }
    
    return $this->setCache('height', ($dimensions = $this->get('dimensions')) ? substr($dimensions, strpos($dimensions, 'x')+1) : null);
  }

  public function isWritable()
  {
    return is_writable($this->getFullPath());
  }

  public function checkFileExists($orDelete = false)
  {
    if (!$this->get('file'))
    {
      return false;
    }

    $exists = file_exists($this->getFullPath());

    if (false === $exists && $orDelete && $this->exists())
    {
      $this->delete();
    }

    return $exists;
  }

  public function __toString()
  {
    return $this->getRelPath();
  }

  public function getFullPath()
  {
    return dmOs::join(sfConfig::get('sf_upload_dir'), $this->getRelPath());
  }

  public function getRelPath()
  {
    return trim($this->get('Folder')->get('rel_path').'/'.$this->get('file'), '/');
  }

  public function getWebPath()
  {
    return sfConfig::get('sf_upload_dir_name').'/'.$this->getRelPath();
  }

  public function getFullWebPath()
  {
    return dm::getRequest()->getAbsoluteUrlRoot().'/'.$this->getWebPath();
  }

  public function isImage()
  {
    return 'image' === $this->getMimeGroup();
  }

  public function getMimeGroup()
  {
    return substr($this->get('mime'), 0, strpos($this->get('mime'), '/'));
  }
  
  /**
   * @return dmImage
   */
  public function getImage()
  {
    if(!$this->isImage())
    {
      throw new dmException($this.' is not an image');
    }

    return new dmImage($this->getFullPath(), $this->get('mime'));
  }

  /**
   * Physically creates asset
   *
   * @param string $asset_path path to the asset original file
   * @param bool $move do move or just copy ?
   */
  public function create(sfValidatedFile $file)
  {
    $this->file = $this->getAvailableFileName(dmString::slugify(dmOs::getFileWithoutExtension($file->getOriginalName())).dmOs::getFileExtension($file->getOriginalName(), true));

    $this->clearCache();

    $file->save($this->getFullPath());

    $this->refreshFromFile();

    return $this;
  }

  /**
   * Physically replaces asset
   */
  public function replaceFile(sfValidatedFile $file)
  {
    $this->destroy();
    
    return $this->create($file);
  }

  /*
   * if this file already exists in the folder,
   * add a numeric suffix not to override the first one
   */
  protected function getAvailableFileName($fileName)
  {
    if(!file_exists(dmOs::join($this->get('Folder')->getFullPath(), $fileName)))
    {
      return $fileName;
    }

    $name = pathinfo($fileName, PATHINFO_FILENAME);
    $extension = pathinfo($fileName, PATHINFO_EXTENSION);

    if(!preg_match('/_\d+$/', $name))
    {
      $name .= '_1';
    }
    $number = (int) preg_replace('/.+_(\d+)$/', '$1', $name);

    while($this->get('Folder')->hasFile($name.'.'.$extension))
    {
      ++$number;
      $name = preg_replace('/(.+)_\d+$/', '$1_'.$number, $name);
    }

    return $name.'.'.$extension;
  }

  /**
   * @return DmMedia the new media with $toMedia values
   */
  public function copyTo(DmMedia $toMedia)
  {
    $toMedia->set('file', $this->get('file'));

    if (!copy($this->getFullPath(), $toMedia->getFullPath()))
    {
      throw new dmException(sprintf(
        'Can not copy from %s to %s',
        $this->getFullPath(),
        $toMedia->getFullPath()
      ));
    }
    
    $toMedia->fromArray(array(
      'legend'      => $this->get('legend'),
      'author'      => $this->get('author'),
      'license'     => $this->get('license'),
      'mime'        => $this->get('mime'),
      'dimensions'  => $this->get('dimensions')
    ));
    
    return $toMedia;
  }

  public function move(DmMediaFolder $folder)
  {
    if($folder->id == $this->dm_media_folder_id)
    {
      return $this;
    }

    if(!$this->isWritable())
    {
      throw new dmException(sprintf('The file %s is not writable.', dmProject::unRootify($this->fullPath)));
    }

    if(!$folder->isWritable())
    {
      throw new dmException(sprintf('The folder %s is not writable.', dmProject::unRootify($folder->fullPath)));
    }

    if($folder->hasSubFolder($this->file))
    {
      throw new dmException(sprintf('The selected folder already contains a folder named "%s".', $this->name));
    }

    if($folder->hasFile($this->file))
    {
      throw new dmException(sprintf('The selected folder already contains a file named "%s".', $this->name));
    }

    rename($this->fullPath, $folder->fullPath.'/'.$this->file);

    $this->dm_media_folder_id = $folder->id;
    $this->Folder = $folder;
    $this->save();

    return $this;
  }

  public function refreshFromFile()
  {
    $this->size = filesize($this->getFullPath());

    if($mimeTypeResolver = $this->getService('mime_type_resolver'))
    {
      $this->mime = $mimeTypeResolver->getByFilename($this->getFullPath(), 'application/force-download');
    }
    
    /*
     * Important to set dimensions without reload data
     */
    $this->set('dimensions', null, false);
    $this->clearCache();

    return $this;
  }

  /**
   * Physically remove assets
   */
  protected function destroy()
  {
    if ($this->isImage())
    {
      $this->destroyThumbnails();
    }

    if ($this->checkFileExists() && $this->getService('filesystem'))
    {
      $this->getService('filesystem')->unlink($this->getFullPath());
    }

    return !$this->checkFileExists();
  }

  public function destroyThumbnails()
  {
    if (!$this->isImage() || !$this->getService('filesystem'))
    {
      return true;
    }
    
    $thumbs = sfFinder::type('file')
    ->name(dmOs::getFileWithoutExtension($this->get('file')).'*')
    ->maxdepth(0)
    ->in(dmOs::join($this->Folder->getFullPath(), '.thumbs'));

    return $this->getServiceContainer()->getService('filesystem')->unlink($thumbs);
  }


  public function save(Doctrine_Connection $conn = null)
  {
    if (!$this->file)
    {
      throw new dmException('Trying to save DmMedia with empty file field');
    }

    if (!$this->checkFileExists())
    {
      //throw new dmException(sprintf('Trying to save DmMedia with no existing file : %s', $this->file));
    }
    elseif($this->isNew())
    {
      $this->refreshFromFile();
    }

    return parent::save($conn);
  }

  public function postDelete($event)
  {
    parent::postDelete($event);

    if (!$this->destroy())
    {
      throw new dmException('Can not delete '.$this->getFullPath());
    }
  }

}