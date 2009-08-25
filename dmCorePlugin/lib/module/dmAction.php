<?php

class dmAction
{

	protected
	  $key,
	  $params;

	public function __construct($key, $config)
	{
    $this->key = $key;

    $this->params = $config;

    if (!isset($this->params['name']))
    {
    	$this->params['name'] = dmString::humanize($key);
    }

    if (!isset($this->params['type']))
    {
      if (strncmp($key, 'list', 4) === 0)
      {
        $this->params['type'] = 'list';
      }
      elseif (strncmp($key, 'show', 4) === 0)
      {
        $this->params['type'] = 'show';
      }
      else
      {
      	$this->params['type'] = 'simple';
      }
    }
	}

  public function getParam($key, $default = null)
  {
    return isset($this->params[$key]) ? $this->params[$key] : $default;
  }

  public function setParam($key, $value)
  {
    return $this->params[$key] = $value;
  }

  public function getName()
  {
    return $this->getParam('name');
  }

  public function getType()
  {
    return $this->getParam('type');
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