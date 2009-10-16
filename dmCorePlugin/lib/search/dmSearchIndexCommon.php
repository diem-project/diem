<?php

abstract class dmSearchIndexCommon
{
  protected
  $dispatcher,
  $logger,
  $serviceContainer;

  /**
   * The name of this index.
   *
   * @var string
   */
  protected $name;

  /**
   * True if index is setup
   *
   * @var bool
   */
  protected $setup = false;

  /**
   * Sets the index name.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  
  public function getLogger()
  {
    return $this->logger;
  }

  /**
   * Gets the index name.
   *
   * @returns string
   */
  public function getName()
  {
    return $this->name;
  }


  /**
   * Runs the setup routine to make sure the index is in a workable state.
   */
  protected function setup()
  {
    if (!$this->setup)
    {
      $this->configure();

      $this->postSetup();

      $this->setup = true;
    }
  }

  /**
   * A routine that is executed after setting up.
   */
  protected function postSetup()
  {
    // nothing to do
  }

  /**
   * Returns true if index is in a workable state
   *
   * @returns bool 
   */
  public function isSetup()
  {
    return $this->setup;
  }

  /**
   * Runs the internal setup procedure.
   *
   * This method should be overloaded by search indexes.  This method should
   * initialize the service registry and setup the backend engine.
   */
  protected function configure()
  {
    // nothing to do
  }

  /**
   * Runs the initial setup procedure to configure meta information, such as a
   * name.
   */
  protected function initialize()
  {
  }
}