<?php

class dmServerActions extends dmAdminBaseActions
{

	public function executeIndex(dmWebRequest $request)
	{
	}

	public function executePhpinfo()
	{
		phpinfo();
		die;
	}
	
}