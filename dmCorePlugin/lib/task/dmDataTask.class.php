<?php

/**
 * Install Diem
 */
class dmDataTask extends dmServiceTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    parent::configure();
    $this->namespace = 'dm';
    $this->name = 'data';
    $this->briefDescription = 'Ensure required data';

    $this->detailedDescription = <<<EOF
Will provide Diem required data
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    if (!sfContext::hasInstance())
    {
      dm::createContext($this->configuration);
    }

    $databaseManager = new sfDatabaseManager($this->configuration);

    return $this->executeService("dmData", $options);
  }
}
