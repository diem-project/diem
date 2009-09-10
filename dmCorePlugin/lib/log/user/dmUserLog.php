<?php

class dmUserLog
{
	protected
	$dispatcher,
	$filesystem,
	$dir,
	$file;
	
	public function __construct(sfEventDispatcher $dispatcher, dmFileSystem $filesystem)
	{
    $this->dispatcher = $dispatcher;
    $this->filesystem = $filesystem;
    
    $this->initialize();
	}
	
	public function initialize()
	{
    $this->dir  = dmOs::join(sfConfig::get('dm_data_dir'), 'log');
    $this->file = dmOs::join($this->dir, 'user.log');
	}
	
	public function getEntries($max = 0)
	{
		$entries = array();
		
		$jsonLines = array_reverse(file($this->file, FILE_IGNORE_NEW_LINES));
		
    if($max)
    {
    	$jsonLines = array_slice($jsonLines, 0, $max);
    }
		
		foreach($jsonLines as $jsonLine)
		{
			$entries[] = dmUserLogEntry::createFromJson($jsonLine);
		}
		
		return $entries;
	}
	
	public function log(dmContext $dmContext)
	{
    $this->checkFile();
    
    $data = dmUserLogEntry::createFromDmContext($dmContext)->toJson();
    
    if($fp = fopen($this->file, 'a'))
    {
	    fwrite($fp, $data."\n");
	    fclose($fp);
    }
    else
    {
    	throw new dmException(sprintf('Can not log in %s', $this->file));
    }
	}
	
	protected function checkFile()
	{
	  if (!$this->filesystem->mkdir($this->dir))
	  {
      throw new dmException(sprintf('User log dir %s can not be created', $this->dir));
	  }
	  
	  if (!file_exists($this->file))
    {
      if (!touch($this->file))
      {
        throw new dmException(sprintf('User log file %s can not be created', $this->file));
      }
      
      chmod($this->file, 0777);
    }
	}
	
	public function clear()
	{
		$this->checkFile();
		file_put_contents($this->file, '');
	}
	
	public function getSize()
	{
		$this->checkFile();
		return filesize($this->file);
	}
}