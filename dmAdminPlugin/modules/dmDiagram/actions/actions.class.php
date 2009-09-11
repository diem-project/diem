<?php

class dmDiagramActions extends dmAdminBaseActions
{
	public function executeIndex(dmWebRequest $request)
	{
	  $this->dependencyDiagramImage = 'cache/tmp/dependency_diagram.png';
	  
	  $dependencyDiagramImageFullPath = dmOs::join(sfConfig::get('sf_web_dir'), $this->dependencyDiagramImage);
	  
	  $dotFile = dmOs::join(sys_get_temp_dir(), dmString::random(12).'.dot');
	  
    $this->dmContext->getFilesystem()->mkdir(dirname($dependencyDiagramImageFullPath));
    
    $configFiles = $this->context->getConfiguration()->getConfigPaths('config/dm/services.yml');
    $configFiles[] = dmOs::join(dm::getDir(), 'dmFrontPlugin/config/dm/services.yml');
    
    $sc = new sfServiceContainerBuilder;

    $loader = new sfServiceContainerLoaderFileYaml($sc);
    $loader->load($configFiles);
    
    $dumper = new sfServiceContainerDumperGraphviz($sc);

    file_put_contents($dotFile, $dumper->dump(array(
    
    )));
    
    $return = $this->dmContext->getFileSystem()->exec(sprintf('dot -Tpng %s > %s', $dotFile, $dependencyDiagramImageFullPath));
    
    if (!$return)
    {
      $this->getUser()->logError('Diem can not generate the dependency diagram : '.$this->dmContext->getFilesystem()->getLastExec('output'));
    }
	}
  
}