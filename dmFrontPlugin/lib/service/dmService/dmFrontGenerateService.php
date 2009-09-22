<?php

class dmFrontGenerateService extends dmService
{

  public function execute()
  {
    $this->log('Generate front for modules');
    
    foreach(dmContext::getInstance()->getModuleManager()->getProjectModules() as $moduleKey => $module)
    {
      $this->log(sprintf("Generate front for module %s", $moduleKey));

      $actionGenerator = new dmFrontActionGenerator($module, $this->dispatcher);
      if (!$actionGenerator->execute())
      {
        $this->alert('Can NOT create actions for module '.$module);
      }

      $componentGenerator = new dmFrontComponentGenerator($module, $this->dispatcher);
      if (!$componentGenerator->execute())
      {
        $this->alert('Can NOT create components for module '.$module);
      }

      $actionTemplateGenerator = new dmFrontActionTemplateGenerator($module, $this->dispatcher);
      if (!$actionTemplateGenerator->execute())
      {
        $this->alert('Can NOT create action templates for module '.$module);
      }
    }
  }
 
}