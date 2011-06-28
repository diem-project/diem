<?php

abstract class dmBaseTask extends sfBaseTask
{

  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', 'admin'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev')
    ));
  }

  protected function executeTask($name, $arguments = array(), $options = array())
  {
    $taskClass = $name."Task";

    if (!class_exists($taskClass))
    {
      throw new dmException($taskClass." does not exists");
    }

    $task = new $taskClass($this->dispatcher, $this->formatter);
    
    return $task->run($arguments, $options);
  }

}