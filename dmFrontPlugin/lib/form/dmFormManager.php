<?php

class dmFormManager implements ArrayAccess
{
  protected
  $forms;
  
  public function __construct()
  {
    $this->initialize();
  }
  
  public function initialize()
  {
    $this->forms = array();
  }
  
  /**
   * Returns true if the parameter exists (implements the ArrayAccess interface).
   *
   * @param  string  $name  The parameter name
   *
   * @return Boolean true if the parameter exists, false otherwise
   */
  public function offsetExists($name)
  {
    return array_key_exists($name, $this->forms);
  }

  /**
   * Returns a parameter value (implements the ArrayAccess interface).
   *
   * @param  string  $name  The parameter name
   *
   * @return mixed  The parameter value
   */
  public function offsetGet($name)
  {
    if (!array_key_exists($name, $this->forms))
    {
      throw new InvalidArgumentException(sprintf('The form manager has no "%s" form.', $name));
    }

    return $this->forms[$name];
  }

  /**
   * Sets a parameter (implements the ArrayAccess interface).
   *
   * @param string  $name   The parameter name
   * @param mixed   $value  The parameter value 
   */
  public function offsetSet($name, $value)
  {
    if (!$value instanceof dmForm)
    {
      throw new InvalidArgumentException(sprintf('The object "%s" is not an instance of dmForm', get_class($value)));
    }
    
    $this->forms[$name] = $value;
  }

  /**
   * Removes a parameter (implements the ArrayAccess interface).
   *
   * @param string $name    The parameter name
   */
  public function offsetUnset($name)
  {
    unset($this->forms[$name]);
  }
}