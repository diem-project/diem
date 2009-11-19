<?php

class dmAdminBreadCrumb
{
  protected
  $context,
  $i18n,
  $helper,
  $record;
  
  public function __construct(dmContext $context, dmI18n $i18n, dmHelper $helper)
  {
    $this->context  = $context;
    $this->i18n     = $i18n;
    $this->helper   = $helper;
    
    $this->initialize();
  }
  
  protected function initialize()
  {
    
  }
  
  public function connect()
  {
    $this->context->getEventDispatcher()->connect('admin.edit_object', array($this, 'listenToEditObjectEvent'));
  }
  
  public function listenToEditObjectEvent(sfEvent $event)
  {
    $this->setRecord($event['object']);
  }
  
  public function setRecord(dmDoctrineRecord $record)
  {
    $this->record = $record;
  }
  
  public function getLinks()
  {
    if ($this->context->isModuleAction('dmAdmin', 'index'))
    {
      return array();
    }
    
    $module = $this->record ? $this->record->getDmModule() : $this->context->getModuleManager()->getModuleOrNull(
      $this->context->getRequest()->getParameter('module')
    );
    
    $links = array();
    
    $links['home'] = array();
    
    if ($module)
    {
      $space = $module->getSpace();
      $type  = $space->getType();
        
      $links['module_type']   = array('type' => $type);
      $links['module_space']  = array('space' => $space);
      $links['module']        = array('module' => $module);
      
      if ($this->record)
      {
        $links['object'] = array('object' => $this->record);
      }
      elseif(($action = $this->context->getActionName()) !== 'index')
      {
        $links['action'] = array('action' => dmString::humanize('create' === $action ? 'new' : $action));
      }
    }
    
    /*
     * Allow listeners of dm.bread_crumb.filter_links event
     * to filter and modify the links list
     */
    return $this->context->getEventDispatcher()->filter(new sfEvent($this, 'dm.bread_crumb.filter_links'), $links)->getReturnValue();
  }
  
  public function renderHomeLink(array $options = array())
  {
    return $this->helper->£link()
    ->text(£('span.s16block.s16_home_gray', '&nbsp;'))
    ->title($this->i18n->__('Home'))->set('.home');
  }
  
  public function renderModuleTypeLink(array $options = array())
  {
    return $this->helper->£link($this->context->getRouting()->getModuleTypeUrl($options['type']))
    ->text($this->i18n->__($options['type']->getPublicName()));
  }
  
  public function renderModuleSpaceLink(array $options = array())
  {
    return $this->helper->£link($this->context->getRouting()->getModuleSpaceUrl($options['space']))
    ->text($this->i18n->__($options['space']->getPublicName()));
  }
  
  public function renderModuleLink(array $options = array())
  {
    return dmArray::get($options, 'last')
    ? $this->helper->£('h1', $this->i18n->__($options['module']->getPlural()))
    : $this->helper->£link('@'.$options['module']->getUnderscore())
    ->text($this->i18n->__($options['module']->getPlural()));
  }
  
  public function renderActionLink(array $options = array())
  {
    return $this->helper->£('h1', __($options['action']));
  }
  
  public function renderObjectLink(array $options = array())
  {
    return dmArray::get($options, 'last')
    ? $this->helper->£('h1', $options['object']->__toString())
    : $this->helper->£link($options['object']);
  }
  
  public function renderRawLink($html)
  {
    return $html;
  }
  
  public function renderLinksArray(array $links)
  {
    $html = array();
    $nbLinks = count($links);
    
    $it = 0;
    
    foreach($links as $type => $options)
    {
      if (is_string($type))
      {
        $method = 'render'.dmString::camelize($type).'Link';
      
        if (++$it === $nbLinks)
        {
          $options['last'] = true;
        }
        
        $html[$type] = $this->$method($options);
      }
      else
      {
        $html[$type] = $options;
      }
    }
    
    return $html;
  }
  
  public function render()
  {
    $t = dmDebug::timerOrNull('dmAdminBreadCrumb::render');
    
    $links = $this->renderLinksArray($this->getLinks());
    
    if (empty($links))
    {
      $html = '';
    }
    else
    {
      $html =
      $this->helper->£o('div#breadCrumb.mt10.clearfix').
      $this->helper->£('ol', '<li>'.implode('</li><li class="sep">&gt;</li><li>', $links).'</li>');
      
      if ($miniSearchForm = dmArray::get($this->context->getResponse()->getSlots(), 'dm.mini_search_form'))
      {
        $html .= $this->helper->£('div.dm_mini_search_form', $miniSearchForm);
      }
      
      $html .= $this->helper->£c('div');
    }
    
    $t && $t->addTime();
    
    return $html;
  }
}