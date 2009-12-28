<?php

/*
 * Manage windows to be displayed on admin homepage.
 * 
 * Allow plugins and project to filter and modify the windows
 * by listening the dm.admin_homepage.filter_windows event:
 * ---------------------------------------------------------------------
 * $this->dispatcher->connect('dm.admin_homepage.filter_windows', array($this, 'listenToFilterWindowsEvent'));
 * ---------------------------------------------------------------------
 * public function listenToFilterWindowsEvent(sfEvent $event, array $windows)
 * {
 *   // add a myWindow in second column
 *   $windows[1]['myWindow'] = array($this, 'renderMyWindow');
 *   
 *   // change the existing weekChart renderer
 *   $windows[0]['weekChart'] = array($this, 'renderWeekChart');
 *   
 *   return $windows;
 * }
 * ---------------------------------------------------------------------
 * public function renderMyWindow(dmHelper $helper)
 * {
 *   // render the window with the $helper
 * }
 * ---------------------------------------------------------------------
 */
class dmAdminHomepageManager
{
  protected
  $dispatcher,
  $helper,
  $windows;
  
  public function __construct(sfEventDispatcher $dispatcher, dmHelper $helper)
  {
    $this->dispatcher = $dispatcher;
    $this->helper     = $helper;
  }
  
  protected function getDefaultWindows()
  {
    // foreach column, declare the windows with render callback
    return array(
      array(
        'weekChart'     => array($this, 'renderWeekChart'),
        'contentChart'  => array($this, 'renderContentChart'),
        'browserChart'  => array($this, 'renderBrowserChart')
      ),
      array(
        'visitChart'    => array($this, 'renderVisitChart'),
        'logChart'      => array($this, 'renderLogChart')
      ),
      array(
        'requestLog'    => array($this, 'renderRequestLog'),
        'eventLog'      => array($this, 'renderEventLog')
      )
    );
  }
  
  protected function getWindows()
  {
    return $this->dispatcher->filter(
      new sfEvent($this, 'dm.admin_homepage.filter_windows'),
      $this->getDefaultWindows()
    )->getReturnValue();
  }
  
  public function render()
  {
    $html = $this->helper->£o('div.dm_admin_homepage_columns.clearfix');

    foreach($this->getWindows() as $column => $windows)
    {
      $html .= $this->renderColumn($windows);
    }
    
    return $html.$this->helper->£c('div');
  }
  
  protected function renderColumn(array $windows)
  {
    $html = $this->helper->£o('div.dm_admin_homepage_column');
    
    foreach($windows as $window)
    {
      $html .= $this->renderWindow($window);
    }
    
    return $html.$this->helper->£c('div');
  }
  
  protected function renderWindow($window)
  {
    if(is_callable($window))
    {
      return call_user_func_array($window, array($this->helper));
    }
    else
    {
      return $window;
    }
  }
  
  /*
   * Default window renderers
   */
  public function renderWeekChart(dmHelper $helper)
  {
    return $helper->renderComponent('dmChart', 'little', array('name' => 'week'));
  }
  
  public function renderContentChart(dmHelper $helper)
  {
    return $helper->renderComponent('dmChart', 'little', array('name' => 'content'));
  }
  
  public function renderBrowserChart(dmHelper $helper)
  {
    return $helper->renderComponent('dmChart', 'little', array('name' => 'browser'));
  }
  
  public function renderVisitChart(dmHelper $helper)
  {
    return $helper->renderComponent('dmChart', 'little', array('name' => 'visit'));
  }
  
  public function renderLogChart(dmHelper $helper)
  {
    return $helper->renderComponent('dmChart', 'little', array('name' => 'log'));
  }
  
  public function renderRequestLog(dmHelper $helper)
  {
    return $helper->renderComponent('dmLog', 'little', array('name' => 'request'));
  }
  
  public function renderEventLog(dmHelper $helper)
  {
    return $helper->renderComponent('dmLog', 'little', array('name' => 'event'));
  }
}