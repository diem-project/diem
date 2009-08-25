<?php

require_once(sfConfig::get('dm_core_dir').DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'context'.DIRECTORY_SEPARATOR.'dmContext.php');

class dmFrontContext extends dmContext
{
	protected
	  $page,
	  $pageHelper;

  public function getPage()
  {
    return $this->page;
  }

  public function getPageHelper()
  {
  	if (is_null($this->pageHelper))
  	{
  		$pageHelperClass = dm::getUser()->can('zone_edit') ? 'dmFrontPageEditHelper' : 'dmFrontPageHelper';
  		$this->pageHelper = new $pageHelperClass($this);
  	}

  	return $this->pageHelper;
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
    $this->page = $page;
  }

  public static function createInstance(sfContext $sfContext)
  {
    return self::$instance = new self($sfContext);
  }
}