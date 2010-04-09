<?php

/**
 * Provides basic methods for configurable services
 */
abstract class dmConfigurable
{
  protected $options;

  /**
   * Configures the current object.
   *
   * @param array $options     An array of options
   *
   * @return dmConfigurable The current object instance
   */
  public function configure(array $options = array())
  {
    $this->options = array_merge(
      null !== $this->options ? $this->options : $this->getDefaultOptions(),
      $options
    );

    return $this;
  }
  
  /**
   * Gets all default options.
   *
   * @return array  An array of named default options
   */
  public function getDefaultOptions()
  {
    return array();
  }
  
  /**
   * Adds a new option value with a default value.
   *
   * @param string $name   The option name
   * @param mixed  $value  The default value
   *
   * @return dmConfigurable The current object instance
   */
  public function addOption($name, $value = null)
  {
    $this->options[$name] = $value;

    return $this;
  }

  /**
   * Changes an option value.
   *
   * @param string $name   The option name
   * @param mixed  $value  The value
   *
   * @return dmConfigurable The current object instance
   *
   * @throws InvalidArgumentException when a option is not supported
   */
  public function setOption($name, $value)
  {
    $this->options[$name] = $value;

    return $this;
  }

  /**
   * Gets an option value.
   *
   * @param  string $name The option name
   *
   * @return mixed  The option value
   */
  public function getOption($name, $default = null)
  {
    return isset($this->options[$name]) ? $this->options[$name] : $default;
  }

  /**
   * Returns true if the option exists.
   *
   * @param  string $name  The option name
   *
   * @return bool true if the option exists, false otherwise
   */
  public function hasOption($name)
  {
    return array_key_exists($name, $this->options);
  }

  /**
   * Gets all options.
   *
   * @return array  An array of named options
   */
  public function getOptions()
  {
    return $this->options;
  }

  /**
   * Sets the options.
   *
   * @param array $options  An array of options
   *
   * @return dmConfigurable The current object instance
   */
  public function setOptions(array $options)
  {
    $this->options = $options;

    return $this;
  }

  /**
   * Gets the option or sets it if $value is not $setIfNot.
   *
   * @param string $name      The option name
   * @param mixed  $value     The value
   * @param mixed  $setIfNot  Set the option if the value is not
   *
   * @return dmConfigurable The current object instance
   *
   * @throws InvalidArgumentException when a option is not supported
   */
  public function getOrSetOption($name, $value, $setIfNot = null)
  {
    if($value === $setIfNot)
    {
      return $this->getOption($name);
    }

    return $this->setOption($name, $value);
  }

  public function mergeOption($name, array $value)
  {
    return $this->setOption($name, array_merge($this->getOption($name, array()), $value));
  }
  
}