<?php
/*
 * This file is part of the diem package.
 * (c) Stéphane Erard <stephane.erard@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This file is responsible for managing security features of modules.
 * It is mainly used by dmModuleManagerConfigHandler when generating
 * dmAdmin and dmFront modules.
 *
 * @author serard
 *
 */
class dmModuleSecurityManager
{
  /**
   * Used to store the context of execution.
   * It is the dmContext name.
   * Can be admin or front.
   *
   * @var string admin || front
   */
  protected $context;


  /**
   * @var dmModule
   */
  protected $module;

  /**
   * Constructor
   *
   * @param string $context admin || front
   * @return dmSecurityManager
   */
  public function __construct($app)
  {
    $this->app = $app;
    $this->context = dmContext::getInstance($app);
  }

  /**
   * Secures a module according to its options
   * @param dmModule $module
   */
  public function secureModule(dmModule $module)
  {
    $securityConfig = $module->getOption('security', array());
    $securityConfig = $securityConfig[$this->app];

    foreach($securityConfig['actions'] as $actionName=>$actionConfig){
      $this->secureModuleAction($module, $actionName, $actionConfig);
    }

    foreach($securityConfig['components'] as $componentName=>$componentConfig){
      $this->secureModuleComponent($module, $componentName, $componentConfig);
    }
  }

  public function secureModuleAction(dmModule $module, $actionName, $actionConfig)
  {
    $strategy = $actionConfig['strategy'];
    $stratege = $this->context->getServiceContainer()->getService(sprintf('%s_module_security_%s_strategy', $this->app, $strategy));
    $stratege->secure($module, $actionName, $actionConfig);
  }

  public function secureModuleComponent(dmModule $module, $componentName, $componentConfig)
  {
    $strategy = $actionConfig['strategy'];
    $stratege = $this->context->getServiceContainer()->getService(sprintf('admin_component_security_%s_strategy', $this->app, $strategy));
    $stratege->secure($module, $actionName, $actionConfig);
  }
}