<?php

class dmModuleType
{
  protected
    $name,
    $spaces,
    $slug;

  public function initialize($name, array $spaces = array())
  {
    $this->name   = $name;
    $this->spaces = $spaces;
    $this->slug   = null;
  }

  /**
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


  public function getSpaces()
  {
    return $this->spaces;
  }

  public function hasSpaces()
  {
    return count($this->spaces);
  }


  public function getSpace($name)
  {
    return $this->spaces[$name];
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