<?php

class dmWidgetType
{

  protected
    $module,
    $action,
    $params;

  public function __construct($module, $action, $config = array())
  {
    $this->module  = $module;
    $this->action  = $action;

    $name = trim(dmArray::get($config, "name", dmString::humanize($action)));

    $this->params = array(
      'name'       => $name,
      'form_class' => $config['form_class'],
      'view_class' => $config['view_class'],
      'full_key'   => $module.dmString::camelize($action),
      'use_component' => $config['use_component']
    );
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
  	return $this->getParam('full_key');
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
  	return $this->getParam('use_component');
  }

  public function getName()
  {
    return $this->getParam('name');
  }

  public function getFormClass()
  {
    return $this->getParam('form_class');
  }

  public function getViewClass()
  {
    return $this->getParam('view_class');
  }

  public function getUnderscore()
  {
  	return dmString::underscore($this->getModule());
  }

  public function getParam($key)
  {
    return isset($this->params[$key]) ? $this->params[$key] : null;
  }

  public function setParam($key, $value)
  {
    return $this->params[$key] = $value;
  }

  public function __toString()
  {
    return $this->getKey();
  }

}