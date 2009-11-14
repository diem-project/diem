<?php

class dmFrontGenerateTask extends dmContextTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', 'front'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
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
    $this->log('Generate front for modules');
    
    foreach($this->get('module_manager')->getProjectModules() as $moduleKey => $module)
    {
      $this->log(sprintf("Generate front for module %s", $moduleKey));

      $actionGenerator = new dmFrontActionGenerator($module, $this->dispatcher, $this->get('filesystem'));
      
      if (!$actionGenerator->execute())
      {
        $this->logBlock('Can NOT create actions for module '.$module);
      }

      $componentGenerator = new dmFrontComponentGenerator($module, $this->dispatcher, $this->get('filesystem'));
      
      if (!$componentGenerator->execute())
      {
        $this->logBlock('Can NOT create components for module '.$module);
      }

      $actionTemplateGenerator = new dmFrontActionTemplateGenerator($module, $this->dispatcher, $this->get('filesystem'));
      
      if (!$actionTemplateGenerator->execute())
      {
        $this->logBlock('Can NOT create action templates for module '.$module);
      }
    }
  }

}