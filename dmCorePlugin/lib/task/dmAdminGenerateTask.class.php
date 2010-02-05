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

    $existingModules = array();

    foreach($modules as $moduleKey => $module)
    {
      if ($pluginName = $module->getPluginName())
      {
        if($module->isOverridden())
        {
          continue;
        }
        
        $module->setOption('generate_dir', dmOs::join($this->configuration->getPluginConfiguration($pluginName)->getRootDir(), 'modules', $module->getSfName()));
      }
      else
      {
        $module->setOption('generate_dir', dmOs::join(sfConfig::get('sf_apps_dir'), 'admin/modules', $module->getSfName()));
      }
    
      if(is_dir($module->getOption('generate_dir')))
      {
        $existingModules[] = $moduleKey;
      }
    }
    
    $moduleToClear = dmArray::get($options, 'clear');
    
    foreach($modules as $moduleKey => $module)
    {
      if ($moduleToClear && $moduleKey !== $moduleToClear)
      {
//        $this->log('Skip '.$moduleKey);
        continue;
      }
      if (!$module->hasModel() || !$module->hasAdmin())
      {
//        $this->log('Skip module without model nor admin '.$moduleKey);
        continue;
      }
      if (in_array($moduleKey, $existingModules) && !$moduleToClear)
      {
//        $this->log('Skip existing module '.$moduleKey);
        continue;
      }

      $task = new dmAdminDoctrineGenerateModuleTask($this->dispatcher, $this->formatter);
      $task->setCommandApplication($this->commandApplication);
      $task->setConfiguration($this->configuration);
  
      $task->run(array('admin', $module->getKey(), $module->getModel()), array(
        'theme'                 => 'dmAdmin',
        'env'                   => $options['env'],
        'route-prefix'          => $module->getUnderscore(),
        'with-doctrine-route'   => true,
        'generate-in-cache'     => true,
        'non-verbose-templates' => true,
        'singular'              => $moduleKey,
        'plural'                => $moduleKey.'s',
        'from-admin-generate'   => 'true'
      ));
    }

    $this->get('cache_manager')->clearAll();
  }
}
