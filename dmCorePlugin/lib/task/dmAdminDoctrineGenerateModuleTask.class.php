<?php

class dmAdminDoctrineGenerateModuleTask extends sfDoctrineGenerateModuleTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    parent::configure();

    $this->aliases = array();
    $this->namespace = 'dmAdmin';
    $this->name = 'generate-module';
    $this->briefDescription = 'Generates a Diem module';
  }

  protected function executeGenerate($arguments = array(), $options = array())
  {
    // generate module
    $tmpDir = sfConfig::get('sf_cache_dir').DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.md5(uniqid(rand(), true));
    $generatorManager = new sfGeneratorManager($this->configuration, $tmpDir);
    $generatorManager->generate('dmDoctrineGenerator', array(
      'model_class'           => $arguments['model'],
      'moduleName'            => $arguments['module'],
      'theme'                 => $options['theme'],
      'non_verbose_templates' => $options['non-verbose-templates'],
      'with_show'             => $options['with-show'],
      'singular'              => $options['singular'],
      'plural'                => $options['plural'],
      'route_prefix'          => $options['route-prefix'],
      'with_doctrine_route'   => false
    ));

    $moduleDir = sfConfig::get('sf_app_module_dir').'/'.$arguments['module'];

    // copy our generated module
    $this->getFilesystem()->mirror($tmpDir.DIRECTORY_SEPARATOR.'auto'.ucfirst($arguments['module']), $moduleDir, sfFinder::type('any'));

    if (!$options['with-show'])
    {
      $this->getFilesystem()->remove($moduleDir.'/templates/showSuccess.php');
    }

    // change module name
    $finder = sfFinder::type('file')->name('*.php');
    $this->getFilesystem()->replaceTokens($finder->in($moduleDir), '', '', array('auto'.ucfirst($arguments['module']) => $arguments['module']));

    // customize php and yml files
    $finder = sfFinder::type('file')->name('*.php', '*.yml');
    $this->getFilesystem()->replaceTokens($finder->in($moduleDir), '##', '##', $this->constants);

    // create basic test
    $this->getFilesystem()->copy(sfConfig::get('sf_symfony_lib_dir').DIRECTORY_SEPARATOR.'task'.DIRECTORY_SEPARATOR.'generator'.DIRECTORY_SEPARATOR.'skeleton'.DIRECTORY_SEPARATOR.'module'.DIRECTORY_SEPARATOR.'test'.DIRECTORY_SEPARATOR.'actionsTest.php', sfConfig::get('sf_test_dir').DIRECTORY_SEPARATOR.'functional'.DIRECTORY_SEPARATOR.$arguments['application'].DIRECTORY_SEPARATOR.$arguments['module'].'ActionsTest.php');

    // customize test file
    $this->getFilesystem()->replaceTokens(sfConfig::get('sf_test_dir').DIRECTORY_SEPARATOR.'functional'.DIRECTORY_SEPARATOR.$arguments['application'].DIRECTORY_SEPARATOR.$arguments['module'].'ActionsTest.php', '##', '##', $this->constants);

    // delete temp files
    $this->getFilesystem()->remove(sfFinder::type('any')->in($tmpDir));
  }

  protected function executeInit($arguments = array(), $options = array())
  {
    $moduleObject = dmContext::getInstance()->getModuleManager()->getModule($arguments['module']);

    if ($moduleObject->isProject())
    {
      $moduleDir = sfConfig::get('sf_app_module_dir').'/'.$arguments['module'];
    }
    else
    {
      $moduleDir = dmOs::join(sfConfig::get('dm_admin_dir'), 'modules', $arguments['module']);
    }

    // create basic application structure
    $finder = sfFinder::type('any')->discard('.sf');
    $dirs = $this->configuration->getGeneratorSkeletonDirs('dmAdminDoctrineModule', $options['theme']);

    foreach ($dirs as $dir)
    {
      if (is_dir($dir))
      {
        $this->getFilesystem()->mirror($dir, $moduleDir, $finder);
        break;
      }
    }

    // move configuration file
    if (file_exists($config = $moduleDir.'/lib/configuration.php'))
    {
      if (file_exists($target = $moduleDir.'/lib/'.$arguments['module'].'GeneratorConfiguration.class.php'))
      {
        $this->getFilesystem()->remove($config);
      }
      else
      {
        $this->getFilesystem()->rename($config, $target);
      }
    }

    // move helper file
    if (file_exists($config = $moduleDir.'/lib/helper.php'))
    {
      if (file_exists($target = $moduleDir.'/lib/'.$arguments['module'].'GeneratorHelper.class.php'))
      {
        $this->getFilesystem()->remove($config);
      }
      else
      {
        $this->getFilesystem()->rename($config, $target);
      }
    }

    // move form file
    if (file_exists($config = $moduleDir.'/lib/form.php'))
    {
      if (file_exists($target = $moduleDir.'/lib/'.$arguments['model'].'AdminForm.php'))
      {
        $this->getFilesystem()->remove($config);
      }
      else
      {
        $this->getFilesystem()->rename($config, $target);
      }
    }

    // move export file
    if (file_exists($config = $moduleDir.'/lib/export.php'))
    {
      if (file_exists($target = $moduleDir.'/lib/'.$arguments['model'].'AdminExport.class.php'))
      {
        $this->getFilesystem()->remove($config);
      }
      else
      {
        $this->getFilesystem()->rename($config, $target);
      }
    }

    // create basic test
//    $this->getFilesystem()->copy(sfConfig::get('sf_symfony_lib_dir').DIRECTORY_SEPARATOR.'task'.DIRECTORY_SEPARATOR.'generator'.DIRECTORY_SEPARATOR.'skeleton'.DIRECTORY_SEPARATOR.'module'.DIRECTORY_SEPARATOR.'test'.DIRECTORY_SEPARATOR.'actionsTest.php', sfConfig::get('sf_test_dir').DIRECTORY_SEPARATOR.'functional'.DIRECTORY_SEPARATOR.$arguments['application'].DIRECTORY_SEPARATOR.$arguments['module'].'ActionsTest.php');

    // customize test file
//    $this->getFilesystem()->replaceTokens(sfConfig::get('sf_test_dir').DIRECTORY_SEPARATOR.'functional'.DIRECTORY_SEPARATOR.$arguments['application'].DIRECTORY_SEPARATOR.$arguments['module'].'ActionsTest.php', '##', '##', $this->constants);

    // customize php and yml files
    $finder = sfFinder::type('file')->name('*.php', '*.yml');
    $this->constants['CONFIG'] = sprintf(<<<EOF
    model_class:           %s
    theme:                 %s
    non_verbose_templates: %s
    with_show:             %s
    route_prefix:          %s
    with_doctrine_route:   %s
EOF
    ,
      $arguments['model'],
      $options['theme'],
      $options['non-verbose-templates'] ? 'true' : 'false',
      $options['with-show'] ? 'true' : 'false',
      $options['route-prefix'] ? $options['route-prefix'] : '~',
      'false'
    );

    $this->getFilesystem()->replaceTokens($finder->in($moduleDir), '##', '##', $this->constants);

    $generatorFile = dmOs::join($moduleDir, 'config/generator.yml');

    $generatorBuilder = new myAdminGeneratorBuilder($moduleObject);

    file_put_contents(
      $generatorFile,
      $generatorBuilder->getTransformed(file_get_contents($generatorFile))
    );
  }
}
