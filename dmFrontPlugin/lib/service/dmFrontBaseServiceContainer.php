<?php

abstract class dmFrontBaseServiceContainer extends dmBaseServiceContainer
{
  
  protected function loadParameters(array $parameters = array())
  {
    parent::loadParameters();

    $this->addParameters(array('theme_manager.options' => array(
      'list' => sfConfig::get('dm_theming_themes', sfConfig::get('dm_theme_list')) // BC 5.0_BETA6
    )));
    
    $this->setParameter('context.page', null);
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

    $this->getService('helper_extension')->connect();
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
    if ($this->getService('user')->can('zone_add, widget_add, widget_edit_fast'))
    {
      $this->setParameter('page_helper.class', $this->getParameter('page_helper.edit_class'));
    }
    /*
     * User can not edit front so we load the view only class
     */
    else
    {
      $this->setParameter('page_helper.class', $this->getParameter('page_helper.view_class'));
    }
  }
}