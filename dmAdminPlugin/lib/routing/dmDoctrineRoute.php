<?php

class dmDoctrineRoute extends sfDoctrineRoute
{

  /**
   * Constructor.
   *
   * @param string $pattern       The pattern to match
   * @param array  $defaults      An array of default parameter values
   * @param array  $requirements  An array of requirements for parameters (regexes)
   * @param array  $options       An array of options
   *
   * @see sfObjectRoute
   */
  public function __construct($pattern, array $defaults = array(), array $requirements = array(), array $options = array())
  {
    /*
     * Remove .html from urls
     */
    unset($defaults['sf_format']);
    $pattern = str_replace('.:sf_format', '', $pattern);
    
//    if (empty($options['method']))
//    {
//      $options['method'] = 'fetchJoinAll';
//    }
    
    parent::__construct($pattern, $defaults, $requirements, $options);
  }
  
  public function getOption($name)
  {
    return $this->options[$name];
  }
  
  public function isType($type)
  {
    return $this->options['type'] == $type;
  }
}