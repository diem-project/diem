<?php

class dmModuleSpace
{

	protected
	  $slug,
	  $type,
	  $name,
	  $modules;

	public function __construct($name, $modules, dmModuleType $type)
	{
		$this->name = $name;
		$this->type = $type;
		$this->modules = array();

		$module_class = $type->isProject() ? 'dmProjectModule' : 'dmModule';

		foreach($modules as $moduleKey => $moduleConfig)
		{
			$moduleKey = dmString::modulize($moduleKey);

			$this->modules[$moduleKey] = new $module_class($moduleKey, $moduleConfig, $this);
		}
	}

	public function getType()
	{
		return $this->type;
	}

	public function getName()
	{
		return $this->name;
	}

  public function getSlug()
  {
    if(is_null($this->slug))
    {
      $this->slug = dmString::slugify(dm::getI18n()->__($this->getName()));
    }
    return $this->slug;
  }

	public function getModules()
	{
		return $this->modules;
	}

	public function hasModules()
	{
		return count($this->modules);
	}

}