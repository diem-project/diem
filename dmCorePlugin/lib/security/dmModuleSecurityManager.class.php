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
class dmModuleSecurityManager extends dmModuleSecurityStrategy
{
  /**
   * @var dmModule
   */
  protected $module;

  public function setApp($app)
  {
    $this->app = $app;
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
    $stratege = $this->getStratege($strategy, 'module');
    $stratege->secure($module, $actionName, $actionConfig);
  }

  public function secureModuleComponent(dmModule $module, $componentName, $componentConfig)
  {
    $strategy = $actionConfig['strategy'];
    $stratege = $this->getStratege($strategy, 'component');
    $stratege->secure($module, $actionName, $actionConfig);
  }

  public function parseCredentials($credentials)
  {
    $parser = new sfYamlParser();
    return $parser->parse($credentials);
  }

  public function getStratege($strategy, $for)
  {
    return $this->context->getServiceContainer()->getService(sprintf('%s_security_%s_strategy', $for, $strategy));
  }

  public function setGenerateDirOption(dmModule $module, $app, $configuration)
  {
    if ($pluginName = $module->getPluginName())
    {
      if($module->isOverridden())
      {
        return;
      }

      $module->setOption('generate_dir', dmOs::join($configuration->getPluginConfiguration($pluginName)->getRootDir(), 'modules', $module->getSfName()));
    }
    else
    {
      $module->setOption('generate_dir', dmOs::join(sfConfig::get('sf_apps_dir'), $app.'/modules', $module->getSfName()));
    }
  }

  /**
   * Returns the security.yml as array
   * If file doesnt exist, returns an empty array
   * @param dmModule $module
   * @return array the array representation of the security.yml file for the specified dmModule $module
   */
  public function getSecurityYaml(dmModule $module, $app, $configuration)
  {
    $yaml = array();
    if(!$module->getOption('generate_dir', false))
    {
      $this->setGenerateDirOption($module, $app, $configuration);
    }
    if(file_exists($filepath = $this->getSecurityFilepath($module)))
    {
      $yaml = sfYaml::load($filepath);
    }
    return $yaml;
  }

  /**
   * Returns security.yml path for specified module
   *
   * @param dmModule $module
   * @return string the path to security.yml for specified module
   */
  public function getSecurityFilepath(dmModule $module)
  {
    return dmOs::join($module->getOption('generate_dir'), 'config', 'security.yml');
  }
}