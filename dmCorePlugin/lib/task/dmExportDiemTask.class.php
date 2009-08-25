<?php

class dmExportDiemTask extends dmServiceTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
  	parent::configure();

    $this->addOptions(array(
      new sfCommandOption('scp', null, sfCommandOption::PARAMETER_REQUIRED, 'Send to server', false),
    ));

  	$this->namespace = 'dm';
    $this->name = 'export-diem';
    $this->briefDescription = 'Exporte diem en .tgz dans le dossier de cache';

    $this->detailedDescription = 'Exporte diem en .tgz dans le dossier de cache';
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
  	return $this->executeService("dmExportDiem", $options);
  }

}