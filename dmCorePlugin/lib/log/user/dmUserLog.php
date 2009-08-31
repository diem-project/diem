<?php

class dmUserLog
{
	protected
	$file;
	
	public function __construct()
	{
		$this->file = dmOs::join(sfConfig::get('sf_data_dir'), 'dm_user.log');
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
	  if (!file_exists($this->file))
    {
      if (!touch($this->file))
      {
        throw new dmException('Log file %s can not be created', $this->file);
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