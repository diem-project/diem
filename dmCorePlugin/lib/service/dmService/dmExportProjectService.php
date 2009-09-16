<?php

class dmExportProjectService extends dmService
{

  public function execute()
  {
  	$proj = dmConfig::getProjectKey();
  	$tar_name = $proj."_".date("y-m-d_H-i-s");
    $exclude = array(
      '$PROJ/.svn',
      '$PROJ/.*',
      '$PROJ/lib/model/om/*',
      '$PROJ/lib/model/map/*',
      '$PROJ/lib/form/base/*',
      '$PROJ/lib/filter/base/*',
      '$PROJ/log/*',
      '$PROJ/cache/*'
    );
    $command = sprintf(
      'PROJ="'.$proj.'";cd ..; tar -czf '.$tar_name.'.tgz $PROJ %s %s ; cd $PROJ',
      '--exclude='.implode(" --exclude=", $exclude),
      $this->getOption("with-uploads") ? '' : '--exclude='.sfConfig::get("sf_upload_dir")."/*"
    );

    $this->log($command);

    if (!$this->filesystem->exec($command))
    {
    	$this->alert($this->filesystem->getLastExec('output'));
    }
  }

}