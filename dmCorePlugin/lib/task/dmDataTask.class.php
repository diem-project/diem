<?php

/**
 * Install Diem
 */
class dmDataTask extends dmContextTask
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
    $this->get('data_load')
    ->setConfiguration($this->configuration)
    ->setLogCallable(array($this, 'customLog'))
    ->execute();
  }

  public function customLog($msg)
  {
    return $this->logSection('diem-data-load', $msg);
  }
}