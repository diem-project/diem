<?php

class dmFrontGenerateTask extends dmServiceTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    parent::configure();

    $this->addOptions(array(
      new sfCommandOption('clear', null, sfCommandOption::PARAMETER_NONE, 'Recreate all module'),
      new sfCommandOption('only', null, sfCommandOption::PARAMETER_OPTIONAL, 'Just for this module', false),
    ));

    $this->namespace = 'dmFront';
    $this->name = 'generate';
    $this->briefDescription = 'Generates front modules';

    $this->detailedDescription = <<<EOF
Will create non-existing front modules
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    if (!sfContext::hasInstance())
    {
      sfContext::createInstance($this->configuration);
    }

    new sfDatabaseManager($this->configuration);

    return $this->executeService("dmFrontGenerate", $options);
  }

}