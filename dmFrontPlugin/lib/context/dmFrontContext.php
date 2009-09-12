<?php

require_once(sfConfig::get('dm_core_dir').DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'context'.DIRECTORY_SEPARATOR.'dmContext.php');

class dmFrontContext extends dmContext
{
	protected
	  $page;
  
  public function getPage()
  {
    return $this->page;
  }

  public function getPageHelper()
  {
    return $this->serviceContainer->getService('page_helper');
  }

  public function getWidgetTypeManager()
  {
    return $this->serviceContainer->getService('widget_type_manager');
  }
  
  /*
   * @return dmModule a project module
   */
  public function getModule()
  {
  	if (!$this->page)
  	{
  		return null;
  	}
  	
    return $this->page->getDmModule();
  }

  public function setPage(DmPage $page = null)
  {
    $this->page = $page;
    
    $this->getPageHelper()->initialize();
  }

  public static function createInstance(sfContext $sfContext)
  {
    return self::$instance = new self($sfContext);
  }
}