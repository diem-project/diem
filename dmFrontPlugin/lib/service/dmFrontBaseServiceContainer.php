<?php

abstract class dmFrontBaseServiceContainer extends dmBaseServiceContainer
{
  protected function loadDependencies(array $dependencies)
  {
    parent::loadDependencies($dependencies);
    
    $this->setService('config_cache',     $dependencies['context']->getConfigCache());
  }
  
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
  
  protected function connectServices()
  {
    parent::connectServices();
    
    if ($this->options['human'] || sfConfig::get('sf_environment') == 'test')
    {
      $this->getService('page_helper')->connect();
      
      $this->getService('layout_helper')->connect();
    }
  }
  
  protected function configureUser()
  {
    parent::configureUser();
    
    $this->getService('user')->setThemeManager($this->getService('theme_manager'));
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
}