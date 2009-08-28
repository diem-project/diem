<?php

abstract class dmFilter extends sfFilter
{
	protected
	$dmContext;
	
  public function initialize($context, $parameters = array())
  {
  	parent::initialize($context, $parameters);
  	
  	$this->dmContext = dmContext::getInstance();
  	
  	return true;
  }
}