<?php

abstract class dmFilter extends sfFilter
{
  protected
  $request;

  /**
   * Initializes this Filter.
   *
   * @param sfContext $context    The current application context
   * @param array     $parameters An associative array of initialization parameters
   *
   * @return boolean true
   */
  public function initialize($context, $parameters = array())
  {
    $this->request = $context->getRequest();

    return parent::initialize($context, $parameters);
  }

  public function getService($name, $class = null)
  {
    return $this->context->get($name, $class);
  }
}