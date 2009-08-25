<?php

class dmBackup
{
	protected
	$dir,
	$filesystem;
	
  protected static
  $defaultDir = 'data/backup';
	
	public function __construct($dir = null)
	{
		$this->dir = is_null($dir)
		? dmOs::join(dmProject::getRootDir(), sfConfig::get('dm_backup_dir', self::$defaultDir))
		: $dir;
    
    $this->filesystem = new dmFilesystem();
    
    $this->checkDirIsWritable();
	}
	
	public function clear()
	{
		return $this->filesystem->deleteDirContent($this->getDir());
	}
	
	/*
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
			return false;
		}
		
		$relFile = dmProject::unRootify($file);
		
		$backupPath = dmOs::join($this->getDir(), dirname($relFile));
		
		if(!$this->filesystem->mkdir($backupPath))
		{
			throw new dmException('Can not create backup dir '.$backupPath);
			return false;
		}
		
		$backupFile = dmOs::join($backupPath, basename($relFile).'.'.date('Y-m-d_H-i-s'));
		
		if (!copy($file, $backupFile))
		{
			throw new dmException('Can not copy '.$file.' to '.$backupFile);
			return false;
		}
		
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
			throw new dmException('dmBackup dir is not writable : '.$this->getDir());
		}
	}
	
	public function getDir()
	{
		return $this->dir;
	}
	
	public function isEnabled()
	{
		return sfConfig::get('dm_backup_enabled');
	}
	
}