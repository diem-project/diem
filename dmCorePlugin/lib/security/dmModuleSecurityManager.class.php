<?php
/*
 * This file is part of the diem package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This class (used as a service using sfServiceContainer) orchestrates
 * the management of security features for modules.
 *
 * Using modules.yml, you can declare which strategy to use to secure your
 * actions and components.
 *
 * Diem comes with two bundled action security strategies:
 *   - action: uses the symfony way to secure actions, using security.yml
 *         		within the config's directory in module directory
 *   - record: adds an overload to secure your actions. user calling a
 *   			module-action must have the right to do so. The rights are
 *   			managed using records of class DmSecureRecord, which
 *   			let you informs the module, action, model and primary key
 *   			of record to secure.
 *   			You can add secure-record permissions to groups and users
 *   			using the dedicated interface in System>Security>Records
 *   			in the admin application.
 *
 * @author serard
 *
 */
class dmModuleSecurityManager extends dmModuleSecurityAbstract implements dmModuleSecurityManagerInterface
{
	/**
	 * Stores the strategies used within a secure() run.
	 * @var array
	 */
	protected $strategies = array();

	/**
	 * @var dmModule
	 */
	protected $module;

	/**
	 * Secures a module according to its options.
	 * We have to go through app/actions|components/config to run the correct
	 * ->secure() method of the correct module securization strategy.
	 * This is what the foreach loops are for.
	 *
	 * @param dmModule $module
	 */
	public function secure(dmModule $module)
	{
		$this->clear();
		$this->module = $module;
		if($security = $module->getOption('security', false))
		{
			if(isset($security[$app = $this->getApplication()]))
			{
				foreach($security[$app] as $actionKind=>$actionsConfig)
				{
					if(!is_array($actionsConfig)) continue;
					foreach($actionsConfig as $actionName=>$actionConfig)
					{
						if(!is_array($actionConfig)) continue;
						if($actionConfig['is_secure'])
						{
							$this->getStrategy($actionConfig['strategy'], $actionKind)->secure($module, $app, $actionName, $actionConfig);
						}
					}
				}
			}
		}
		$this->save();
	}

	/**
	 * On each ->secure() run, we must clear the
	 * existing strategies, so we can reset the running
	 * module.
	 */
	protected function clear()
	{
		$this->clearCache();
		$this->strategies = array();
	}

	/**
	 * When the secure() process is over, we must save
	 * what have to be saved for every used strategies.
	 * This is mainly used so action strategy will not
	 * overwrite many times the security.yml file.
	 */
	protected function save()
	{
		foreach($this->strategies as $strategy)
		{
			$strategy->save();
		}
	}
	/**
	 * (non-PHPdoc)
	 * @see dmModuleSecurityManagerInterface::getStrategy()
	 */
	public function getStrategy($strategy, $actionKind, $module = null, $action = null)
	{
		$serviceStrategy = sprintf('module_security_%s_%s_strategy',$actionKind, $strategy);
		if(!isset($this->strategies[$serviceStrategy]))
		{
			$this->strategies[$serviceStrategy] = $this->container->getService($serviceStrategy)->setModule($this->module instanceof dmModule ? $this->module : $module)->setAction($action);
		}
		return $this->strategies[$serviceStrategy];
	}

	/**
	 * Parses a yaml credential descriptor to its corresponding php.
	 *
	 * @param string $credentials
	 * @return mixed array|string
	 */
	public function parseCredentials($credentials)
	{
		$parser = new sfYamlParser();
		return $parser->parse($credentials);
	}

	/**
	 * Sometimes dmModules don't have generate_dir set, so we must set it ourselves.
	 *
	 * @param dmModule $module
	 * @param unknown_type $configuration
	 */
	public function setGenerateDirOption(dmModule $module, $configuration)
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
			$module->setOption('generate_dir', dmOs::join(sfConfig::get('sf_apps_dir'), $this->getApplication().'/modules', $module->getSfName()));
		}
	}


	/**
	 * Returns the security.yml as array
	 * If file doesnt exist, returns an empty array
	 *
	 * @param dmModule $module
	 * @return array the array representation of the security.yml file for the specified dmModule $module
	 */
	public function getSecurityYaml(dmModule $module)
	{
		$yaml = array();
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

	/**
	 * Saves the yaml array by dumping it as yaml using sfYaml::dump
	 * to the security.yml of the given $module
	 *
	 * @param dmModule $module the module
	 * @param array $securityYaml the php array representation of the security.yml file
	 */
	public function saveSecurityYaml(dmModule $module, $securityYaml)
	{
		$data = sfYaml::dump($securityYaml, 2);
		file_put_contents($this->getSecurityFilepath($module), $data);
	}
}