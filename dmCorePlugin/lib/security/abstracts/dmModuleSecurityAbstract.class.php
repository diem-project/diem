<?php
/*
 *
 *
 */

/**
 *
 * @author serard
 *
 */
class dmModuleSecurityAbstract extends dmMicroCache
{

  /**
   * @var dmContext
   */
  protected $context;

  /**
   * @var sfServiceContainer
   */
  protected $container;
  
  /**
   * @var dmUser
   */
  protected $user;

  /**
   * Constructor.
   * This method is used to construct a child class instance,
   * and helps sfServiceContainer injects dependancies
   *
   * @param dmContext $context
   * @param sfServiceContainer $container
   */
  public function __construct(dmContext $context, sfServiceContainer $container, $user)
  {
    $this->context = $context;
    $this->container = $container;
    $this->user = $user;
  }

  /**
   * Returns the current application (admin|front for example).
   *
   * @return string
   */
  public function getApplication()
  {
    return $this->context->getConfiguration()->getApplication();
  }
}