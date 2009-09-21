<?php

class dmAdminGenerateService extends dmService
{

  public function execute()
  {
    $this->log('Generate admin for modules');

    $modules = dmContext::getInstance()->getModuleManager()->getModules();

    $existing_modules = sfFinder::type('dir')
    ->maxdepth(0)
    ->in(array(
      dmOs::join(sfConfig::get('sf_apps_dir'), 'admin/modules'),
      dmOs::join(sfConfig::get('dm_admin_dir'), 'modules')
    ));

    array_walk($existing_modules, create_function('&$a', '$a = basename($a);'));

    foreach($modules as $moduleKey => $module)
    {
      if ($this->getOption('only') && $module != $this->getOption('only'))
      {
        $this->log("Skipping $module");
        continue;
      }
      if ($module->isProject() && !$module->hasAdmin())
      {
        $this->log(sprintf("Skip module %s wich has no admin", $moduleKey));
        continue;
      }
      if (!$module->isProject() && strncmp($module->getKey(), 'dm', 2) !== 0)
      {
        $this->log(sprintf("Skip module %s wich is nor internal nor project : probably a plugin one", $moduleKey));
        continue;
      }
      if (!$module->hasModel())
      {
        $this->log(sprintf("Skip module %s wich has no associated model", $moduleKey));
        continue;
      }

      if (in_array($moduleKey, $existing_modules))
      {
        if (!$this->getOption('clear') || !$module->isProject())
        {
          $this->log(sprintf("Skip existing module %s", $moduleKey));
          continue;
        }
        else
        {
          $this->log(sprintf("Remove existing module %s", $moduleKey));

          $moduleDir = sfConfig::get('sf_app_module_dir').'/'.$moduleKey;

          dmContext::getInstance()->getFilesystem()->unlink(dmOs::join($moduleDir, 'generator.yml'));
        }
      }

      $this->log(sprintf("Generate admin for module %s", $moduleKey));

      $arguments = array(
        'application' => 'admin',
        'route_or_model' => $module->getKey()
      );
      $options = array(
        '--module='.$moduleKey,
        '--theme='.'dmAdmin',
        '--singular='.$moduleKey,
        '--plural='.$moduleKey.'s',
        '--env='.'dev'
      );
      
      $task = new dmAdminDoctrineGenerateAdminTask($this->dispatcher, $this->formatter);
      $task->run($arguments, $options);
    }

    $this->executeService('dmClearCache');
  }
}