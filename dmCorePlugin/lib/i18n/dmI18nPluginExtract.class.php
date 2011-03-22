<?php
class dmI18nPluginExtract extends sfI18nExtract
{
	protected $extractObjects = array();

	public function configure()
	{
		$this->extractObjects = array();

		$moduleManager = $this->parameters['serviceContainer']->getService('module_manager');
		$modules = $moduleManager->getModules();

		foreach($modules as $moduleObject)
		{
			if(! ($moduleObject->isPlugin() && $moduleObject->getPluginName() === $this->parameters['plugin']->getName())) continue;
			$this->extractObjects[] = new dmI18nModuleExtract($this->i18n, $this->culture, array('module' => $moduleObject));
		}
	}

	public function extractYamlModules()
	{
		$yamlModuleExtractor = new dmI18nYamlModulesExtractor($this->parameters['plugin'], $this->culture);
		$yamlModules = dmOs::join($this->parameters['plugin']->getRootDir(), 'config', 'dm', 'modules.yml');
		if(file_exists($yamlModules))
		{
			$this->yamlModuleExtractor = $yamlModuleExtractor->extract(file_get_contents($yamlModules));
		}
	}

	/**
	 * (non-PHPdoc)
	 * @see sfI18nExtract::extract()
	 * @return dmI18nPluginExtract
	 */
	public function extract()
	{
		$this->logSection('i18n', sprintf('extracting i18n strings for the "%s" plugin for culture "%s" in %s', $this->parameters['plugin']->getName(), $this->culture, $this->parameters['plugin']->getRootDir()));
		$this->extractYamlModules();
		foreach($this->extractObjects as $extract)
		{
			$extract->extract();
		}
		return $this;
	}
	
	public function saveNewMessages()
	{
		$this->save();
	}
	
	public function save()
	{
		$this->yamlModuleExtractor->saveNewMessages();
		foreach($this->extractObjects as $extract)
		{
			$extract->saveNewMessages();
		}
	}
	
	protected function logSection($section, $message, $size = null, $style = 'INFO')
	{
		$this->parameters['logSection']->call($section, $message, $size, $style);
	}
}