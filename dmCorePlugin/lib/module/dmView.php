<?php

class dmView
{

	protected
	  $key,
	  $params,
	  $module;

	public function __construct($key, $config, dmProjectModule $module)
	{
    $this->key = $key;
    $this->module = $module;

    $this->params = array(
      "name" =>     dmArray::get($config, "name", dmString::humanize($key)),
      "options" =>  dmArray::get($config, "options", array())
    );
	}

  public function getParam($key)
  {
    return $this->params[$key];
  }

  public function setParam($key, $value)
  {
    return $this->params[$key] = $value;
  }

  public function getName()
  {
  	return $this->getParam('name');
  }

  public function getKey()
  {
  	return $this->key;
  }

  public function getModule()
  {
  	return $this->module;
  }

  /*
   * Returns rendered partial for this record
   */
  public function render(myDoctrineRecord $record)
  {
    return dmContext::getInstance()->getHelper()->renderPartial(
      $this->module->getKey(),
      'views/'.$this->getKey(),
      array($this->module->getKey() => $record)
    );
  }

  public function __toString()
  {
  	return $this->getKey();
  }
}