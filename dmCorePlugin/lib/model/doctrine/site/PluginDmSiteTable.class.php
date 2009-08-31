<?php
/**
 */
class PluginDmSiteTable extends myDoctrineTable
{
  public function getInstance()
  {
  	if (!$site = $this->createQuery()->withI18n()->dmCache()->fetchOne())
  	{
      $site = dmDb::create('DmSite', array(
        'name' => dmString::humanize(dmProject::getKey())
      ))->saveGet();
  	}
  	
  	return $site;
  }
}