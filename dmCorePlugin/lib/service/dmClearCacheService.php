<?php

class dmClearCacheService extends dmService
{

	public function execute()
	{
		$this->log("clear cache");
    
    dmFileCache::clearAll();

		if(count($survivors = $this->filesystem->find()->maxdepth(0)->in(sfConfig::get("sf_cache_dir"))))
		{
			$this->alert("Can not be removed from cache : ".implode(", ", $survivors));
		}
		else
		{
			$this->log("File cache successfully cleared.");
		}
    
    if (dmAPCCache::isEnabled())
    {
      if(!dmAPCCache::clearAll())
      {
        $this->alert("Can not clear APC cache");
      }
    }

		$this->dispatcher->notify(new sfEvent($this, 'dm.cache.clear'));
	}

}