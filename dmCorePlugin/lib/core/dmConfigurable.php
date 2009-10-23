<?php

abstract class dmConfigurable
{
  protected $options;
  
  /**
   * Configures the current object.
   *
   * @param array $options     An array of options
   */
  public function configure(array $options = array())
  {
    $this->options = array_merge(
      $this->getDefaultOptions(),
      $options
    );
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
  public function getOption($name)
  {
    return isset($this->options[$name]) ? $this->options[$name] : null;
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
  
}