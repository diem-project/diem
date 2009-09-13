<?php

class dmDiagramActions extends dmAdminBaseActions
{
	public function executeIndex(dmWebRequest $request)
	{
	  $this->dicImages = array();
	  
	  foreach(array('admin', 'front') as $appName)
	  {
	    $this->dicImages[$appName] = $this->getDiagramImage($appName);
	  }
	  
	  $doctrineGraphviz = new dmDoctrineGraphviz($this->dmContext->getFilesystem(), $this->context->getConfiguration());
	  
    $this->mldUserImage = $doctrineGraphviz->getMldImage(array(
      'type' => 'user',
      'size' => '12,4'
    ));
    
    $this->mldCoreImage = $doctrineGraphviz->getMldImage(array(
      'type' => 'core',
      'size' => '12,4'
    ));
    
    $this->mldProjectImage = $doctrineGraphviz->getMldImage(array(
      'type' => 'project',
      'size' => '12,4'
    ));
	}
  
	protected function getDiagramImage($appName)
	{
	  $dependencyDiagramImage = sprintf('dependency_diagram_%s.png', $appName);
    
    $dependencyDiagramImageFullPath = dmOs::join(sfConfig::get('sf_cache_dir'), 'web', $dependencyDiagramImage);
    
    $dotFile = dmOs::join(sys_get_temp_dir(), dmString::random(12).'.dot');
    
    if (!$this->dmContext->getFilesystem()->mkdir(dirname($dependencyDiagramImageFullPath)))
    {
      throw new dmException(sprintf('Can not mkdir %s', $dependencyDiagramImageFullPath));
    }
    
    $configFiles = $this->context->getConfiguration()->getConfigPaths('config/dm/services.yml');
    $configFiles[] = dmOs::join(dm::getDir(), sprintf('dm%sPlugin/config/dm/services.yml', dmString::camelize($appName)));
    
    $sc = new sfServiceContainerBuilder;

    $loader = new sfServiceContainerLoaderFileYaml($sc);
    $loader->load($configFiles);
    
    $dumper = new sfServiceContainerDumperGraphviz($sc);

    file_put_contents($dotFile, $dumper->dump(array(
      'graph' => array('concentrate' => 'false', 'bgcolor' => '#fbfbfb', 'ratio' => 'fill', 'size' => '12,4'),
      'node'  => array('fontsize' => 20, 'fontname' => 'Arial', 'shape' => 'Mrecord'),
      'edge'  => array('fontsize' => 9, 'fontname' => 'Arial', 'color' => 'grey', 'arrowhead' => 'open', 'arrowsize' => 0.8),
      'node.instance' => array('fillcolor' => '#9999ff', 'style' => 'filled'),
      'node.definition' => array('fillcolor' => '#eeeeee'),
      'node.missing' => array('fillcolor' => '#ffffff', 'style' => 'filled', 'shape' => 'component'),
    )));
    
    $return = $this->dmContext->getFileSystem()->exec(sprintf('dot -Tpng %s > %s', $dotFile, $dependencyDiagramImageFullPath));
    
    if (!$return)
    {
      $this->getUser()->logError(sprintf('Diem can not generate the %s dependency diagram : %s', $appName, $this->dmContext->getFilesystem()->getLastExec('output')));
    }
    
    return '/cache/'.$dependencyDiagramImage;
	}
	
}