<?php

class dmUserLog
{
	protected
	$dispatcher,
	$filesystem,
	$serviceContainer,
	$file;
	
	public function __construct(sfEventDispatcher $dispatcher, dmFileSystem $filesystem, sfServiceContainer $serviceContainer, array $options = array())
	{
    $this->dispatcher = $dispatcher;
    $this->filesystem = $filesystem;
    $this->serviceContainer = $serviceContainer;
    
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
		  $entry = $this->serviceContainer->getService('user_log_entry');
		  $data = json_decode($jsonLine, true);
		  
		  if (!empty($data))
		  {
		    $entry->setData($data);
			  $entries[] = $entry;
		  }
		}
		
		return $entries;
	}
	
	public function log(dmContext $dmContext)
	{
    $this->checkFile();
    
    $entry = $this->serviceContainer->getService('user_log_entry');
    $entry->configureFromDmContext($dmContext);
    
    $data = $entry->toJson();
    
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