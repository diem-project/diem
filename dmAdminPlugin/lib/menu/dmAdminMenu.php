<?php

class dmAdminMenu
{

  protected
  $dispatcher,
  $user,
  $i18n,
  $routing,
  $moduleManager;

  public function __construct(sfEventDispatcher $dispatcher, dmUser $user, dmI18n $i18n, sfPatternRouting $routing, dmModuleManager $moduleManager)
  {
    $this->dispatcher = $dispatcher;
    $this->user = $user;
    $this->i18n = $i18n;
    $this->routing = $routing;
    $this->moduleManager = $moduleManager;
    
    $this->initialize();
  }
  
  public function initialize()
  {
  }

  public function load()
  {
    $menu = $this->getModuleStructureMenu();
    
    $this->dispatcher->notify(new sfEvent($this, 'dm.admin_menu.loaded', $menu));

    return $menu;
  }
  
  protected function getModuleStructureMenu()
  {
    $menu = array();

    foreach($this->moduleManager->getTypes() as $type_name => $type)
    {
      if ($type->hasSpaces() && ($typeMenu = $this->getTypeMenu($type)))
      {
        if ($type->isProject() && !$this->user->can('content'))
        {
          continue;
        }
        
        $menu[$type_name] = $typeMenu;
      }
    }
    
    return $menu;
  }

  protected function getTypeMenu(dmModuleType $type)
  {
    $spaceMenu = array();
    foreach($type->getSpaces() as $spaceName => $space)
    {
      if ($space->hasModules() && $sm = $this->getSpaceMenu($space))
      {
        $spaceMenu[$spaceName] = $sm;
      }
    }
    
    if(empty($spaceMenu))
    {
      return null;
    }
    
    return array(
      'name' => $this->i18n->__($type->getPublicName()),
      'menu' => $spaceMenu
    );
  }
  
  protected function getSpaceMenu(dmModuleSpace $space)
  {
    $moduleMenu = array();
    foreach($space->getModules() as $moduleKey => $module)
    {
      if(!$module->hasAdmin())
      {
        continue;
      }
      
      if ($module->getParam('credentials') && !$this->user->can($module->getParam('credentials')))
      {
        continue;
      }
      
      $moduleMenu[$moduleKey] = array(
        'name' => $this->i18n->__($module->getPlural()),
        'link' => $this->routing->generate($module->getUnderscore())
      );
    }
    
    if(empty($moduleMenu))
    {
      return null;
    }
    
    return array(
      'name' => $this->i18n->__($space->getName()),
//      'link' => $this->routing->generate('dm_module_space', array('moduleTypeName' => $space->getType()->getSlug(), 'moduleSpaceName' => $space->getSlug())),
      'menu' => $moduleMenu
    );
  }

}