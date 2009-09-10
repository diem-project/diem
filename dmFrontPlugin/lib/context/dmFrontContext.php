<?php

require_once(sfConfig::get('dm_core_dir').DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'context'.DIRECTORY_SEPARATOR.'dmContext.php');

class dmFrontContext extends dmContext
{
	protected
	  $page;

  /**
   * Loads the diem services
   */
  public function loadServiceContainer()
  {
    $configFiles = dmOs::join(sfConfig::get('dm_front_dir'), 'config/dm/services.yml');
    
    parent::doLoadServiceContainer($configFiles);
  }
  
  public function getPage()
  {
    return $this->serviceContainer->getParameter('page');
  }

  public function getPageHelper()
  {
  	return $this->serviceContainer->getService('page_helper');
  }
  
  /*
   * @return dmModule a project module
   */
  public function getModule()
  {
  	if (!$this->getPage())
  	{
  		return null;
  	}
    return $this->getPage()->getDmModule();
  }

  public function setPage(DmPage $page = null)
  {
    $this->serviceContainer->addParameters(array(
      'page' => $page
    ));
    
    $this->getPageHelper()->setPage($page);
  }

  public static function createInstance(sfContext $sfContext)
  {
    return self::$instance = new self($sfContext);
  }
}