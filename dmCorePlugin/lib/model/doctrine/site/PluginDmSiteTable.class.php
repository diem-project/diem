<?php

class PluginDmSiteTable extends myDoctrineTable
{
  
  public function getInstance()
  {
  	if (!$site = $this->createQuery()->dmCache()->fetchOne())
  	{
      $site = dmDb::create('DmSite', array(
        'code' => 'default'
      ))->saveGet();
  	}
  	
  	return $site;
  }
}