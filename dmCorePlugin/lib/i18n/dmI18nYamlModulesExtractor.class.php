<?php
class dmI18nYamlModulesExtractor extends sfI18nYamlExtractor
{
	protected $strings = array();

	protected $pluginConfiguration;
	protected $culture;

	public function __construct($pluginConfiguration, $culture)
	{
		$this->pluginConfiguration = $pluginConfiguration;
		$this->culture = $culture;
	}

	public function extract($content)
	{
		$this->strings = array();

		$config = sfYaml::load($content);

		foreach($config as $space => $types)
		{
			$this->strings[$space] = '';
			foreach($types as $type => $modules)
			{
				$this->strings[$type] = '';
				foreach($modules as $moduleName => $moduleConfig)
				{
					$moduleObject = dmContext::getInstance()->getServiceContainer()->getService('module_manager')->getModule($moduleName);
					if($moduleObject)
					{
						$this->strings[$moduleObject->getName()] = '';
						$this->strings[$moduleObject->getPlural()] = '';
					}
					if(!(isset($moduleConfig['security']) && isset($moduleConfig['security']['admin']) && isset($moduleConfig['security']['admin']['actions']) && is_array($moduleConfig['security']['admin']['actions'])))
					continue;
					foreach($moduleConfig['security']['admin']['actions'] as $actionName => $actionConfig)
					{
						$this->strings[$actionName] = '';
					}
				}
			}
		}

		return $this;
	}

	public function getNewMessages()
	{
		return $this->strings;
	}

	public function saveNewMessages()
	{
		$i18nYaml = dmOs::join($this->pluginConfiguration->getRootDir(), 'data', 'dm', 'i18n', 'en_' . $this->culture . '.yml');
		
		$data = array();
		if(file_exists($i18nYaml))
		{
			$data = sfYaml::load($i18nYaml);
		}
		else{
			if(empty($this->strings)) return;
			@mkdir(dirname($i18nYaml), 0777, true);
		}
		
		foreach($this->strings as $i=>$v)
		{
			if(strlen($i) === 0 || isset($data[$i])) continue;
			$text = strlen($i) === 0 ? $v : $i;
			$data[$text] = '';
		}

		file_put_contents($i18nYaml, sfYaml::dump($data, 2));
	}
}