<?php

class dmClearCacheService extends dmService
{

	public function execute()
	{
		$this->log("clear cache");
    
    dmCacheManager::getInstance()->clearAll();

		if(count($survivors = $this->filesystem->find()->maxdepth(0)->in(sfConfig::get("sf_cache_dir"))))
		{
			$this->alert("Can not be removed from cache : ".implode(", ", $survivors));
		}
		else
		{
			$this->log("File cache successfully cleared.");
		}

		$this->dispatcher->notify(new sfEvent($this, 'dm.cache.clear'));
	}

}