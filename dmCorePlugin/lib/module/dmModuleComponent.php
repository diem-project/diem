<?php

class dmModuleComponent extends dmConfigurable
{
  protected
  $key,
  $baseClass;

  public function __construct($key, array $options)
  {
    $this->key = $key;

    $this->options = $options;
    
    $this->baseClass = 'dmWidget'.dmString::camelize($this->getType());
  }
  
  public function isCachable()
  {
    return $this->getOption('cache', false);
  }

  public function getName()
  {
    return $this->getOption('name');
  }

  public function getType()
  {
    return $this->getOption('type');
  }

  public function getFormClass() 
  {
      return $this->getOption('form_class', $this->baseClass.'Form');      
  }

  public function getViewClass()
  {
      return $this->getOption('view_class', $this->baseClass.'View'); 
  }

  public function getKey()
  {
    return $this->key;
  }

  public function getUnderscore()
  {
    return dmString::underscore($this->getKey());
  }

  public function __toString()
  {
    return $this->getKey();
  }
}