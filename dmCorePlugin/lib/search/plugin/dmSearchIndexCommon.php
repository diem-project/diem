<?php

abstract class dmSearchIndexCommon
{
	/**
   * The logger
   *
   * @var xfLogger
   */
  protected $logger;

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
   * @see xfIndex
   */
  public function __construct()
  {
    $this->name = get_class($this);
    
    $this->setLogger(new dmLoggerBlackhole);

    $this->initialize();
  }

  /**
   * Sets the index name.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
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
   * @see xfIndex
   */
  public function setLogger(dmLogger $logger)
  {
    $this->logger = $logger;
  }

  /**
   * @see xfIndex
   */
  public function getLogger()
  {
    return $this->logger;
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