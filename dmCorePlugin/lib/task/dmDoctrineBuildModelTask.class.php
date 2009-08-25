<?php
/**
 * Will make Model Tables extends myDoctrineTable instead of Doctrine_Table
 */
class dmDoctrineBuildModelTask extends sfDoctrineBuildModelTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
  	parent::configure();

    $this->aliases = array();
    $this->namespace = 'dm';
  }

  /*
   * Replace execute method
   * to set a different packagesPath
   * for diem plugins wich are not
   * in sf_plugins_dir
   */
  protected function execute($arguments = array(), $options = array())
  {
    $this->logSection('doctrine', 'generating model classes');

    $plugins = $this->configuration->getPlugins();
    
    $projectPlugins = array();
    $diemPlugins = array();
    
    foreach($this->configuration->getAllPluginPaths() as $plugin => $path)
    {
      if (!in_array($plugin, $plugins))
      {
        continue;
      }
      
      if (strpos($path, dm::getDir()) === 0)
      {
      	$diemPlugins[$plugin] = $path;
      }
      else
      {
      	$projectPlugins[$plugin] = $path;
      }
    }
    
    $this->buildModelForPlugins($projectPlugins, sfConfig::get('sf_plugins_dir'));
    $this->buildModelForPlugins($diemPlugins, dm::getDir());
    $this->reloadAutoload();
  }

  protected function buildModelForPlugins(array $plugins, $packagesPath)
  {
    $config = $this->getCliConfig();

    $tmpPath = sfConfig::get('sf_cache_dir').DIRECTORY_SEPARATOR.'tmp';

    if (!file_exists($tmpPath))
    {
      Doctrine_Lib::makeDirectories($tmpPath);
    }
    
  	foreach ($plugins as $plugin => $path)
    {
      $this->logSection('doctrine', sprintf('%s in %s', $plugin, $packagesPath));
      $schemas = sfFinder::type('file')->name('*.yml')->in($path.'/config/doctrine');
      foreach ($schemas as $schema)
      {
        $tmpSchemaPath = $tmpPath.DIRECTORY_SEPARATOR.$plugin.'-'.basename($schema);

        $models = Doctrine_Parser::load($schema, 'yml');
        if (!isset($models['package']))
        {
          $models['package'] = $plugin.'.lib.model.doctrine';
          $models['package_custom_path'] = $path.'/lib/model/doctrine';
        }
        Doctrine_Parser::dump($models, 'yml', $tmpSchemaPath);
      }
    }

    $options = array('generateBaseClasses'  => true,
                     'generateTableClasses' => true,
                     'packagesPath'         => $packagesPath,
                     'packagesPrefix'       => 'Plugin',
                     'suffix'               => '.class.php',
                     'baseClassesDirectory' => 'base',
                     'baseClassName'        => 'sfDoctrineRecord');
    $options = array_merge($options, sfConfig::get('doctrine_model_builder_options', array()));

    $import = new Doctrine_Import_Schema();
    $import->setOptions($options);
    $import->importSchema(array($tmpPath, $config['yaml_schema_path']), 'yml', $config['models_path']);
  }
  
}