<?php

class dmRefreshService extends dmService
{

	public function execute()
	{
		$this->executeService('dmClearCache');

		$this->executeService('dmPageSync');

		$this->executeService('dmUpdateSeo');

		if ($this->user && $this->user->can('system'))
		{
			$this->updateIncrementalSkeleton();
		}
	}

	protected function updateIncrementalSkeleton()
	{
		$incrementalSkeletonPath = dmOs::join(sfConfig::get('dm_core_dir'), 'data/incrementalSkeleton');

		foreach(sfFinder::type('dir')->maxDepth(0)->in($incrementalSkeletonPath) as $dir)
		{
			$userPath = sfConfig::get(basename($dir));
			 
			foreach(sfFinder::type('dir')->in($dir) as $skelDir)
			{
				$userDir = dmOs::join($userPath, preg_replace('|^('.preg_quote($dir, '|').')|', '', $skelDir));
				$this->mkdir($userDir);
			}

			foreach(sfFinder::type('file')->in($dir) as $skelFile)
			{
				$userFile = dmOs::join($userPath, preg_replace('|^('.preg_quote($dir, '|').')|', '', $skelFile));
				$this->copy($skelFile, $userFile);
			}
		}
	}

	protected function mkdir($path)
	{
		if (!$this->filesystem->mkdir($path))
		{
			$this->error(sprintf('Can not mkdir %s', $path));
		}
		else
		{
			chmod($path, 0777);
		}
	}

	protected function copy($from, $to)
	{
		if (!file_exists($to))
		{
			if (!copy($from, $to))
			{
				$this->error(sprintf('Can not copy %s to %s', $from, $to));
			}
			else
			{
				chmod($to, 0777);
			}
		}
	}

}