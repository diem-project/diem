<?php

class dmExportDiemService extends dmService
{

  public function execute()
  {
    $tarName = sprintf('diem_%s_%s.tgz',
      sfConfig::get("dm_version"),
      date("y-m-d_H-i-s")
    );
    $exportFile = dmOs::join('cache/'.$tarName);
    $exclude = array(
      '.svn',
      '.settings'
    );
    
    $this->filesystem->exec('pwd');
    $pwd = $this->filesystem->getLastExec('output');
    
    $relDir = $this->filesystem->getRelativeDir($pwd, dm::getDir());
    
    $command = sprintf(
      'tar -czf %s %s %s && chmod 777 %s',
      $exportFile,
      $relDir,
      '--exclude='.implode(" --exclude=", $exclude),
      $exportFile
    );
    
    $this->log($command);

    if (!$this->filesystem->exec($command))
    {
      $this->alert($this->filesystem->getLastExec('output'));
    }

//    $this->log('Diem succesfully saved in '.$exportFile);

    if($server = $this->getOption('scp'))
    {
      $command = sprintf(
        'scp %s %s',
        $exportFile, $server
      );

      $this->log($command);

      if (!$this->filesystem->exec($command))
      {
        $this->alert($this->filesystem->getLastExec('output'));
      }
    }
  }

}