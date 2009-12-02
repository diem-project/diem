<?php

/**
 * Install Diem
 */
class dmAdminGenerateTask extends dmContextTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    parent::configure();

    $this->addOptions(array(
      new sfCommandOption('clear', null, sfCommandOption::PARAMETER_REQUIRED, 'Clear and regenerate a module', null)
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
    $this->logSection('diem', 'Generate admin modules');

    $modules = $this->get('module_manager')->getModules();

    $existingModules = sfFinder::type('dir')
    ->maxdepth(0)
    ->in(array(
      dmOs::join(sfConfig::get('sf_apps_dir'), 'admin/modules'),
      dmOs::join(sfConfig::get('dm_admin_dir'), 'modules')
    ));

    array_walk($existingModules, create_function('&$a', '$a = basename($a);'));
    
    $moduleToClear = dmArray::get($options, 'clear');
    
    foreach($modules as $moduleKey => $module)
    {
      if ($moduleToClear && $moduleKey !== $moduleToClear)
      {
//        $this->logSection('diem', "Skipping $module");
        continue;
      }
      else
      {
        if ($module->isProject() && !$module->hasAdmin())
        {
  //        $this->logSection('diem', sprintf("Skip module %s wich has no admin", $moduleKey));
          continue;
        }
        if (!$module->isProject())
        {
  //        $this->logSection('diem', sprintf("Skip module %s wich is nor internal nor project : probably a plugin one", $moduleKey));
          continue;
        }
        if (!$module->hasModel())
        {
  //        $this->logSection('diem', sprintf("Skip module %s wich has no associated model", $moduleKey));
          continue;
        }
      }

      if (in_array($moduleKey, $existingModules))
      {
        if (!$moduleToClear || !$module->isProject())
        {
//          $this->logSection('diem', sprintf("Skip existing module %s", $moduleKey));
          continue;
        }
        else
        {
          $this->logSection('diem', sprintf("Remove existing module %s", $moduleKey));

          $moduleDir = sfConfig::get('sf_app_module_dir').'/'.$moduleKey;

          $this->get('filesystem')->unlink(dmOs::join($moduleDir, 'generator.yml'));
        }
      }

      $this->logSection('diem', sprintf('Generate admin for module %s', $moduleKey));

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

    $this->get('cache_manager')->clearAll();
  }
}
