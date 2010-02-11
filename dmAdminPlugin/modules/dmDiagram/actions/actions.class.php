<?php

class dmDiagramActions extends dmAdminBaseActions
{
  public function executeIndex(dmWebRequest $request)
  {
    $this->withDispatcherLinks = $request->getParameter('with_dispatcher_links');
    
    $this->loadServiceContainerDumper();
    
    $this->dicImages = array();
    
    try
    {
      foreach(array('front', 'admin') as $appName)
      {
        $this->dicImages[$appName] = $this->getDiagramImage($appName);
      }
    }
    catch(dmException $e)
    {
      $this->getUser()->logError($e->getMessage());
    }
    
    $doctrineGraphviz = new dmDoctrineGraphviz($this->context->getFilesystem(), $this->context->getConfiguration());
    
    try
    {
      $this->mldUserImage = $doctrineGraphviz->getMldImage(array('type' => 'user'));
      $this->mldCoreImage = $doctrineGraphviz->getMldImage(array('type' => 'core'));
      $this->mldProjectImage = $doctrineGraphviz->getMldImage(array('type' => 'project'));
    }
    catch(dmException $e)
    {
      $this->getUser()->logError($e->getMessage());
    }

    $this->getUser()->logInfo('Hold down your mouse button to move the images', false);
  }
  
  protected function loadServiceContainerDumper()
  {
    $this->context->loadServiceContainerExtraStuff();
    
    require_once(dmOs::join(sfConfig::get('dm_core_dir'), 'lib/vendor/sfService/sfServiceContainerDumperGraphviz.php'));
  }
  
  protected function getDiagramImage($appName)
  {
    $dependencyDiagramImage = sprintf('dependency_diagram_%s_%s.png', $appName, time());
    
    $dependencyDiagramImageFullPath = dmOs::join(sfConfig::get('sf_web_dir'), 'cache', $dependencyDiagramImage);
    
    $dotFile = tempnam(sys_get_temp_dir(), 'dm_dependency_');
    
    if (!$this->context->getFilesystem()->mkdir(dirname($dependencyDiagramImageFullPath)))
    {
      throw new dmException(sprintf('Can not mkdir %s', $dependencyDiagramImageFullPath));
    }
    
    $configFiles = array(
      dmOs::join(sfConfig::get('dm_core_dir'), 'config/dm/services.yml'),
      dmOs::join(dm::getDir(), sprintf('dm%sPlugin/config/dm/services.yml', dmString::camelize($appName)))
    );
    
    $projectFile = dmOs::join(sfConfig::get('sf_config_dir'), 'dm/services.yml');
    if (file_exists($projectFile)) $configFiles[] = $projectFile;
    
    $appFile = dmOs::join(sfConfig::get('sf_apps_dir'), $appName, 'config/dm/services.yml');
    if (file_exists($appFile)) $configFiles[] = $appFile;
    
    $sc = new sfServiceContainerBuilder;

    $loader = new sfServiceContainerLoaderFileYaml($sc);
    $loader->load($configFiles);
    
    $sc->setService('dispatcher',       $this->context->getEventDispatcher());
    $sc->setService('user',             $this->context->getUser());
    $sc->setService('response',         $this->context->getResponse());
    $sc->setService('i18n',             $this->getI18n());
    $sc->setService('routing',          $this->context->getRouting());
    $sc->setService('config_cache',     $this->context->getConfigCache());
    $sc->setService('controller',       $this->context->getController());
    $sc->setService('logger',           $this->context->getLogger());
    $sc->setService('module_manager',   $this->context->getModuleManager());
    $sc->setService('context',          $this->context);
    $sc->setService('doctrine_manager', Doctrine_Manager::getInstance());
    
    $dumper = new dmServiceContainerDumperGraphviz($sc);
    $dumper->enableDispatcherLinks($this->withDispatcherLinks);

    file_put_contents($dotFile, $dumper->dump(array(
      'graph' => array(
        'overlap' => 'false',
        'splines' => 'true',
        'epsilon' => '0.5',
        'maxiter' => '30000',
        'concentrate' => 'false',
        'bgcolor' => 'transparent',
        'ratio' => 'fill',
        'size' => '25,12'
      ),
      'node'  => array('fontsize' => 20, 'fontname' => 'Arial', 'shape' => 'Mrecord'),
      'edge'  => array('fontsize' => 9, 'fontname' => 'Arial', 'color' => 'grey', 'arrowhead' => 'open', 'arrowsize' => 1),
      'node.instance' => array('fillcolor' => '#ffffff', 'style' => 'filled', 'shape' => 'component'),
      'node.definition' => array('fillcolor' => 'transparent'),
      'node.missing' => array('fillcolor' => '#ffaaaa', 'style' => 'filled', 'shape' => 'record'),
    )));
    
    $filesystem = $this->context->getFileSystem();
    //$return = $filesystem->exec(sprintf('dot -Tpng %s > %s', $dotFile, $dependencyDiagramImageFullPath));
    //$return = $filesystem->exec(sprintf('twopi -Granksep=5 -Tpng %s > %s', $dotFile, $dependencyDiagramImageFullPath));

    $return = $filesystem->exec(sprintf('neato -Tpng %s > %s', $dotFile, $dependencyDiagramImageFullPath));

    unlink($dotFile);

    if (!$return)
    {
      $this->getUser()->logError(sprintf('Diem can not generate the %s dependency diagram. Probably graphviz is not installed on the server.', $appName), false);
    
      $this->getUser()->logAlert($filesystem->getLastExec('command')."\n".$filesystem->getLastExec('output'), false);
    }
    
    return '/cache/'.$dependencyDiagramImage;
  }
  
}