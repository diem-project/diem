<?php

class dmLoremizeTask extends dmServiceTask
{

  /**
   * @see sfTask
   */
  protected function configure()
  {
    parent::configure();

    $this->addOptions(array(
      new sfCommandOption('model', null, sfCommandOption::PARAMETER_REQUIRED, 'The model name'),
      new sfCommandOption('nb', null, sfCommandOption::PARAMETER_OPTIONAL, 'nb records to create', 30),
    ));

    $this->namespace = 'dm';
    $this->name = 'loremize';
    $this->briefDescription = 'Create random records for a model';

    $this->detailedDescription = <<<EOF
Create random records for a model
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

    $databaseManager = new sfDatabaseManager($this->configuration);

    return $this->executeService("dmLoremize", $options);
  }

}