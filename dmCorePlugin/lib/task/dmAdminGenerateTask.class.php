<?php

/**
 * Install Diem
 */
class dmAdminGenerateTask extends dmServiceTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    parent::configure();

    $this->addOptions(array(
      new sfCommandOption('clear', null, sfCommandOption::PARAMETER_NONE, 'Recreate base classes ( model, form, filter )'),
      new sfCommandOption('only', null, sfCommandOption::PARAMETER_OPTIONAL, 'Just for this module', false),
    ));

    $this->namespace = 'dmAdmin';
    $this->name = 'generate';
    $this->briefDescription = 'Generates admin modules';

    $this->detailedDescription = <<<EOF
Will create non-existing admin modules
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

    new sfDatabaseManager($this->configuration);

    return $this->executeService("dmAdminGenerate", $options);
  }
}
