<?php

class dmUserLog
{
	protected
	$dispatcher,
	$filesystem,
	$file;
	
	public function __construct(sfEventDispatcher $dispatcher, dmFileSystem $filesystem, array $options = array())
	{
    $this->dispatcher = $dispatcher;
    $this->filesystem = $filesystem;
    
    $this->initialize($options);
	}
	
	public function initialize(array $options = array())
	{
    $this->file = dmProject::rootify(dmArray::get($options, 'file', 'data/dm/log/user.log'));
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
	  if (!$this->filesystem->mkdir(dirname($this->file)))
	  {
      throw new dmException(sprintf('User log dir %s can not be created', dirname($this->file)));
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