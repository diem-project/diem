<?php

class myProjectBuilderTask extends dmContextTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', 'front'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev')
    ));
    
    $this->namespace = 'my';
    $this->name = 'project-builder';
    $this->briefDescription = 'Builds a basic project';

    $this->detailedDescription = $this->briefDescription;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $this->log('project builder');

    $builder = new myTestProjectBuilder($this->getContext());
    $builder->execute();
  }
}