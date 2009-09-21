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
  
  public function executeOld()
  {
    $this->log('Generate front for modules');

    $modules = dmContext::getInstance()->getModuleManager()->getProjectModules();

    $existingModules = sfFinder::type('dir')
    ->maxdepth(0)
    ->in(array(
      dmOs::join(sfConfig::get('sf_apps_dir'), 'front/modules')
    ));

    array_walk($existingModules, create_function('&$a', '$a = basename($a);'));

    foreach($modules as $moduleKey => $module)
    {
      if ($this->getOption('only') && $module != $this->getOption('only'))
      {
        $this->log("Skipping $module for testing purpose");
        continue;
      }

      if (in_array($moduleKey, $existingModules))
      {
        if (!$this->getOption('clear'))
        {
          $this->log(sprintf("Skip existing module %s", $moduleKey));
          continue;
        }
        else
        {
          $this->log(sprintf("Remove existing module %s", $moduleKey));
          sfToolkit::clearDirectory(dmOs::join(sfConfig::get('sf_apps_dir'), 'front/modules', $moduleKey));
        }
      }

      $this->log(sprintf("Generate front for module %s", $moduleKey));

      $actionGenerator = new dmFrontActionGenerator($module, $this->dispatcher);
      if (!$actionGenerator->execute())
      {
        $this->log('Can NOT create actions for module '.$module);
      }

      $componentGenerator = new dmFrontComponentGenerator($module, $this->dispatcher);
      if (!$componentGenerator->execute())
      {
        $this->log('Can NOT create components for module '.$module);
      }

      $actionTemplateGenerator = new dmFrontActionTemplateGenerator($module, $this->dispatcher);
      if (!$actionTemplateGenerator->execute())
      {
        $this->log('Can NOT create action templates for module '.$module);
      }

      if ($module->hasModel())
      {
        $viewTemplateGenerator = new dmFrontViewTemplateGenerator($module, $this->dispatcher);
        if (!$viewTemplateGenerator->execute())
        {
          $this->log('Can NOT create view templates for module '.$module);
        }
      }
    }

    $this->executeService('dmClearCache');
  }
}