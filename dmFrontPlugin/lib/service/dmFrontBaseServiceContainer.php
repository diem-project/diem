<?php

abstract class dmFrontBaseServiceContainer extends dmBaseServiceContainer
{
  
  protected function loadParameters(array $parameters = array())
  {
    parent::loadParameters();
    
    $this->addParameters(array(
      'theme_manager.options' => array(
        'list'    => sfConfig::get('dm_theme_list'),
        'default' => sfConfig::get('dm_theme_default')
      )
    ));
  }

  public function configureServices()
  {
    parent::configureServices();
    
    $this->configurePageHelper();
  }
  
  public function connect()
  {
    parent::connect();
    
    $this->getService('dispatcher')->connect('dm.context.change_page', array($this, 'listenToContextChangePageEvent'));
  }
  
  protected function connectServices()
  {
    parent::connectServices();
    
    $this->getService('page_helper')->connect();
  }
  
  /**
   * Listens to the dm.context.change_page event.
   *
   * @param sfEvent An sfEvent instance
   */
  public function listenToContextChangePageEvent(sfEvent $event)
  {
    $this->setParameter('context.page', $event['page']);
  }
  
  protected function configurePageHelper()
  {
    /*
     * If user can edit front, the page helper service will use the edit class,
     * and it will require the user to check its credentials
     */
    if ($this->getService('user')->can('front_edit'))
    {
      $this->setParameter('page_helper.class', $this->getParameter('page_helper.edit_class'));
      
      $this->getService('page_helper')->setUser($this->getService('user'));
    }
    /*
     * User can not edit front so we load the view only class
     */
    else
    {
      $this->setParameter('page_helper.class', $this->getParameter('page_helper.view_class'));
    }
  }

  /*
   * @return dmFrontLinkTag
   */
  public function getLinkTag($resource)
  {
    if (!$resource instanceof dmFrontLinkResource)
    {
      $resource = $this->getLinkResource($resource);
    }
    
    $this->setParameter('link_tag.class', $this->getParameter('link_tag_'.$resource->getType().'.class'));
    $this->setParameter('link_tag.source', $resource);
    
    return $this->getService('link_tag');
  }
  
  /*
   * return @dmFrontDoctrinePager
   */
  public function getDoctrinePager($model, $maxPerPage)
  {
    $this->setParameter('doctrine_pager.model', $model);
    $this->setParameter('doctrine_pager.max_per_page', $maxPerPage);
    
    return $this->getService('doctrine_pager');
  }
}