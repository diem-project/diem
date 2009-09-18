<?php

class dmExportProjectTask extends dmServiceTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    parent::configure();

    $this->addOptions(array(
      new sfCommandOption('with-uploads', null, sfCommandOption::PARAMETER_NONE, 'Include uploads dir')
    ));

    $this->namespace = 'dm';
    $this->name = 'export-project';
    $this->briefDescription = 'Exporte le projet en .tgz dans le dossier parent';

    $this->detailedDescription = 'Exporte le projet en .tgz dans le dossier parent';
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    return $this->executeService("dmExportProject", $options);
  }

}