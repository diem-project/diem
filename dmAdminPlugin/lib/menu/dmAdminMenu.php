<?php

class dmAdminMenu
{

	protected
	$menu,
	$config,
	$user;
	
	protected static $i18n;

	public function __construct(dmUser $user)
	{
		self::$i18n = dm::getI18n();
		$this->user = $user;
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
			if ($type->hasSpaces() && ($tm = $this->getTypeMenu($type)))
			{
			  if ($type->isProject() && !$this->user->can('content'))
			  {
			    continue;
			  }
			  
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
    
    if(empty($spaceMenu))
    {
      return null;
    }
    
    return array(
      'name' => self::__($type->getPublicName()),
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
    	
    	if ($module->getParam('credentials') && !$this->user->can($module->getParam('credentials')))
    	{
    	  continue;
    	}
    	
	    $moduleMenu[$moduleKey] = array(
	      'name' => self::__($module->getPlural()),
	      'link' => array('sf_route' => $module->getUnderscore())
	    );
    }
    
    if(empty($moduleMenu))
    {
    	return null;
    }
    
    return array(
      'name' => self::__($space->getName()),
      'link' => array('sf_route' => 'dm_module_space', 'moduleTypeName' => $space->getType()->getSlug(), 'moduleSpaceName' => $space->getSlug()),
      'menu' => $moduleMenu
    );
  }

  protected static function __($text, $arguments = array(), $catalogue = null)
  {
    return self::$i18n->__($text, $arguments, $catalogue);
  }

}