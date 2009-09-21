<?php

class dmAdminDoctrineGenerateAdminTask extends sfDoctrineGenerateAdminTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    parent::configure();

    $this->aliases = array();
    $this->namespace = 'dmAdmin';
    $this->name = 'generate-admin';
    $this->briefDescription = 'Generates a Diem admin module';
  }


  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $module = dmContext::getInstance()->getModuleManager()->getModule(dmString::modulize($arguments['route_or_model']));
    $arguments['module'] = $module;
    $arguments['route_name'] = $module->getUnderscore();

    return $this->generateForRoute($arguments, $options);
  }

  protected function generateForRoute($arguments, $options)
  {
    // execute the doctrine:generate-module task

    $module = $arguments['module'];

    $task = new dmAdminDoctrineGenerateModuleTask($this->dispatcher, $this->formatter);
    $task->setCommandApplication($this->commandApplication);
    $task->setConfiguration($this->configuration);
    
    $this->logSection('app', sprintf('Generating admin module "%s" for model "%s"', $module->getKey(), $module->getModel()));

    return $task->run(array($arguments['application'], $module->getKey(), $module->getModel()), array(
      'theme'                 => $options['theme'],
      'env'              => $options['env'],
      'route-prefix'          => $module->getUnderscore(),
      'with-doctrine-route'   => true,
      'generate-in-cache'     => true,
      'non-verbose-templates' => true,
      'singular'              => $options['singular'],
      'plural'                => $options['plural'],
    ));
    
  }
}