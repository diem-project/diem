<?php

class dmUserLog
{
	protected
	$file;
	
	public function __construct()
	{
		$this->file = dmOs::join(sfConfig::get('sf_data_dir'), 'dm_user.log');
	}
	
	public function log(dmContext $dmContext)
	{
    $this->checkFile();
    
    $data = str_replace('\/', '/', json_encode($this->getData($dmContext)));
    
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
	
	protected function getData(dmContext $dmContext)
	{
		return array(
		  'uri'           => $_SERVER['REQUEST_URI'],
		  'code'          => $dmContext->getSfContext()->getResponse()->getStatusCode(),
		  'app'           => sfConfig::get('sf_app'),
		  'time'          => $_SERVER['REQUEST_TIME'],
		  'ip'            => $_SERVER['REMOTE_ADDR'],
		  'session_id'    => session_id(),
		  'user_id'       => $dmContext->getSfContext()->getUser()->getGuardUserId(),
		  'user_agent'    => $_SERVER['HTTP_USER_AGENT'],
		  'timer'         => sprintf('%.0f', (microtime(true) - dm::getStartTime()) * 1000)
		);
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
}