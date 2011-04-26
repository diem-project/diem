<?php

class dmModuleSpace
{

  protected
    $name,
    $type,
    $modules,
    $slug;
  
  public function initialize($name, dmModuleType $type, array $modules = array())
  {
    $this->name     = $name;
    $this->type     = $type;
    $this->modules  = $modules;
    $this->slug     = null;
  }

  public function getType()
  {
    return $this->type;
  }

  public function getName()
  {
    return $this->name;
  }

  public function getPublicName()
  {
    return $this->name;
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