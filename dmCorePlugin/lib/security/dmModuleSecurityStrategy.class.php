<?php
class dmModuleSecurityStrategy
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
   * Constructor
   * @param dmContext
   * @param sfServiceContainer
   */
  public function __construct(dmContext $context, sfServiceContainer $container)
  {
    $this->context = $context;
    $this->container = $container;
  }
}