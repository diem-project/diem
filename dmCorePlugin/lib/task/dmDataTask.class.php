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

    $this->addOption('load-doctrine-data', 'l', sfCommandOption::PARAMETER_NONE, 'Run doctrine:data-load after loading basic data');

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
    if($options['load-doctrine-data'])
    {
      $this->runTask('doctrine:data-load', array(), array('append'=>true));
    }
  }

  public function customLog($msg)
  {
    return $this->logSection('diem-data-load', $msg);
  }
}