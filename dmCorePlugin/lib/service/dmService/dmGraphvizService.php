<?php

class dmGraphvizService extends dmService
{
	public function execute($is_web = false)
	{
		$this->filesystem->mkdir(sfConfig::get('sf_cache_dir').'/dm/graph');
	  $fileDotName = sfConfig::get('sf_root_dir').'/graph/propel.schema.dot';
	  $fileImgName = sfConfig::get('sf_cache_dir').'/dm/graph/uml-schema';
    $format = 'png';

    $command = sprintf(
      'dot -T%s %s -o %s',
      $format,
      $fileDotName,
      $fileImgName.'.'.$format
    );

    if(!$is_web)
    {
      $this->log("graphviz");
      $task = new sfPropelGraphvizTask($this->dispatcher, $this->formatter);
      $task->run(array(), array());

      $this->log($command);
    }
    else
    {
      $sfCommand = sprintf('propel:graphviz');
      if (!$this->filesystem->sf($sfCommand))
      {
        $this->alert($this->filesystem->getLastExec('output'));
      }
    }
    if (!$this->filesystem->exec($command))
    {
      $this->alert($this->filesystem->getLastExec('output'));
    }
	}
}