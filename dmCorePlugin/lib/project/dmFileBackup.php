<?php

class dmFileBackup extends dmConfigurable
{
  protected
  $dispatcher,
  $filesystem;
  
  public function __construct(sfEventDispatcher $dispatcher, dmFilesystem $filesystem, array $options = array())
  {
    $this->dispatcher = $dispatcher;
    $this->filesystem = $filesystem;
    
    $this->initialize($options);
  }
  
  public function initialize(array $options = array())
  {
    $this->configure($options);
    
    $this->checkDirIsWritable();
  }
  
  public function getDefaultOptions()
  {
    return array(
      'dir' => 'data/dm/backup/filesystem'
    );
  }
  
  public function getDir()
  {
    return dmProject::rootify($this->options['dir']);
  }
  
  public function setDir($dir)
  {
    $this->setOption('dir', dmProject::rootify($dir));
    
    $this->checkDirIsWritable();
  }
  
  public function clear()
  {
    return $this->filesystem->deleteDirContent($this->getDir());
  }
  
  /**
   * Backup a file
   * return boolean success
   */
  public function save($file)
  {
    if (!$this->isEnabled())
    {
      return true;
    }
    
    if(!dmProject::isInProject($file))
    {
      $file = dmOs::join(dmProject::getRootDir(), $file);
    }
    
    if (!is_readable($file))
    {
      throw new dmException('Can no read '.$file);
    }
    
    $relFile = dmProject::unRootify($file);
    
    $backupPath = dmOs::join($this->getDir(), dirname($relFile));
    
    if(!$this->filesystem->mkdir($backupPath))
    {
      throw new dmException('Can not create backup dir '.$backupPath);
    }
    
    $backupFile = dmOs::join($backupPath, basename($relFile).'.'.date('Y-m-d_H-i-s'));
    
    if (!$this->filesystem->touch($backupFile, 0777))
    {
      throw new dmException('Can not copy '.$file.' to '.$backupFile);
    }
    
    file_put_contents($backupFile, file_get_contents($file));
    
    $this->filesystem->chmod($file, 0777);
    
    return true;
  }
  
  public function getFiles()
  {
    return sfFinder::type('file')->in($this->getDir());
  }
  
  protected function checkDirIsWritable()
  {
    if (!$this->filesystem->mkdir($this->getDir()))
    {
      throw new dmException('dmFileBackup dir is not writable : '.$this->getDir());
    }
  }
  
  public function isEnabled()
  {
    return sfConfig::get('dm_backup_enabled');
  }

  public function isFileBackup($file)
  {
    return 0 === strpos($file, $this->getDir());
  }
  
}