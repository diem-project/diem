<?php

require_once(sfConfig::get('dm_core_dir').DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'context'.DIRECTORY_SEPARATOR.'dmContext.php');

class dmFrontContext extends dmContext
{
  protected
    $page;
  
  /*
   * @return DmPage the current page object
   */
  public function getPage()
  {
    return $this->page;
  }

  /*
   * @return dmModule a project module
   */
  public function getModule()
  {
    if (null === $this->page)
    {
      return null;
    }
    
    return $this->page->getDmModule();
  }

  public function setPage(DmPage $page)
  {
    $this->page = $page;
    
    $this->sfContext->getEventDispatcher()->notify(new sfEvent($this, 'dm.context.change_page', array('page' => $page)));
  }

  public static function createInstance(sfContext $sfContext)
  {
    return self::$instance = new self($sfContext);
  }
}