<?php

class dmAdminMenu
{

	protected
	$menu,
	$config,
	$i18n;

	public function __construct()
	{
		$this->i18n = dm::getI18n();
		$this->menu = $this->buildMenu();
	}

	public function getMenu()
	{
		return $this->menu;
	}

	protected function buildMenu()
	{
		$menu = array();

		foreach(dmModuleManager::getTypes() as $type_name => $type)
		{
			if ($type->hasSpaces() && $tm = $this->getTypeMenu($type))
			{
			  $menu[$type_name] = $tm;
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
    
    return array(
      'name' => $this->__($type->getPublicName()),
      //'link' => array('sf_route' => 'dm_module_type', 'moduleTypeName' => $type->getSlug()),
      'menu' => $spaceMenu
    );
  }
  
  protected function getSpaceMenu(dmModuleSpace $space)
  {
    $moduleMenu = array();
    foreach($space->getModules() as $moduleKey => $module)
    {
    	if ($module->isProject() && !$module->getDir())
    	{
    		continue;
    	}
    	
	    $moduleMenu[$moduleKey] = array(
	      'name' => $this->__($module->getPlural()),
	      'link' => array('sf_route' => $module->getUnderscore())
	    );
    }
    
    if(!count($moduleMenu))
    {
    	return null;
    }
    
    return array(
      'name' => $this->__($space->getName()),
      'link' => array('sf_route' => 'dm_module_space', 'moduleTypeName' => $space->getType()->getSlug(), 'moduleSpaceName' => $space->getSlug()),
      'menu' => $moduleMenu
    );
  }

  protected function __($text, $arguments = array(), $catalogue = null)
  {
    return $this->i18n->__($text, $arguments, $catalogue);
  }

}