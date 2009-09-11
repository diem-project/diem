<?php

class dmModuleType
{

  protected
    $name,
    $slug,
    $spaces;

  public function __construct($name, $config)
  {
    $this->name = $name;
    $this->spaces = array();

    foreach($config as $space_name => $modules)
    {
      $this->spaces[$space_name] = new dmModuleSpace($space_name, $modules, $this);
    }
  }

  /*
   * Bouh
   */
  public function isProject()
  {
  	return $this->name == 'Project';
  }

  public function getName()
  {
    return $this->name;
  }

  public function getPublicName()
  {
  	return $this->isProject() ? 'Content' : $this->name;
  }

  public function getSlug()
  {
  	if(null === $this->slug)
  	{
  		$this->slug = dmString::slugify(dm::getI18n()->__($this->getPublicName()));
  	}
  	return $this->slug;
  }

  public function getSpaces()
  {
    return $this->spaces;
  }

  public function hasSpaces()
  {
    return count($this->spaces);
  }


  public function getSpace($space_name)
  {
    return $this->spaces[$space_name];
  }

  public function getSpaceBySlug($slug, $default = null)
  {
    foreach($this->getSpaces() as $space)
    {
    	if($space->getSlug() === $slug)
    	{
    		return $space;
    	}
    }
    return false;
  }

  public function getModules()
  {
  	$modules = array();
  	foreach($this->getSpaces() as $space)
  	{
  		$modules = array_merge($modules, $space->getModules());
  	}
  	return $modules;
  }

}