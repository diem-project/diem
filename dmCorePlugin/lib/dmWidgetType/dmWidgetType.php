<?php

class dmWidgetType extends dmConfigurable
{
  protected
    $module,
    $action;

  public function __construct($module, $action, $options = array())
  {
    $this->module  = $module;
    $this->action  = $action;

    $this->configure($options);
  }

  public function getModule()
  {
    return $this->module;
  }

  public function getAction()
  {
    return $this->action;
  }

  public function getFullKey()
  {
    return $this->getOption('full_key');
  }
  
  public function isCachable()
  {
    return (bool) $this->getOption('cache');
  }
  
  public function isStatic()
  {
    return 'static' === $this->getOption('cache');
  }

  public function getNewWidget()
  {
    return dmDb::create('DmWidget', array(
      'module' => $this->getModule(),
      'action' => $this->getAction()
    ));
  }

  public function useComponent()
  {
    return $this->getOption('use_component');
  }

  public function getName()
  {
    return $this->getOption('name');
  }
  
  public function getPublicName()
  {
    return $this->getOption('public_name');
  }

  public function getFormClass()
  {
    return $this->getOption('form_class');
  }

  public function getViewClass()
  {
    return $this->getOption('view_class');
  }

  public function getUnderscore()
  {
    return dmString::underscore($this->getModule());
  }

  public function __toString()
  {
    return $this->getFullKey();
  }

}