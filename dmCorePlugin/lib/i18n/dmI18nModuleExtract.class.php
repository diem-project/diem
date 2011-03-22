<?php
class dmI18nModuleExtract extends sfI18nExtract
{
	protected $module;

	public function configure()
	{
		if (!isset($this->parameters['module']))
		{
			throw new sfException('You must give a "module" parameter when extracting for a module.');
		}

		$this->module = $this->parameters['module'];

		$options = $this->i18n->getOptions();
		$dirs = $this->i18n->isMessageSourceFileBased($options['source']) ? $this->i18n->getConfiguration()->getI18NDirs($this->module) : null;
		$this->i18n->setMessageSource($dirs, $this->culture);
	}

	public function extract()
	{
		// Extract from PHP files to find __() calls in actions/ lib/ and templates/ directories
		$moduleDir = $this->module->getGenerationDir();
		$this->extractFromPhpFiles(array(
		$moduleDir.'/actions',
		$moduleDir.'/lib',
		$moduleDir.'/templates',
		));

		// Extract from generator.yml files
		$generator = $moduleDir.'/config/generator.yml';
		if (file_exists($generator))
		{
			$yamlExtractor = new sfI18nYamlGeneratorExtractor();
			$this->updateMessages($yamlExtractor->extract(file_get_contents($generator)));
		}

		// Extract from validate/*.yml files
		$validateFiles = glob($moduleDir.'/validate/*.yml');
		if (is_array($validateFiles))
		{
			foreach ($validateFiles as $validateFile)
			{
				$yamlExtractor = new sfI18nYamlValidateExtractor();
				$this->updateMessages($yamlExtractor->extract(file_get_contents($validateFile)));
			}
		}
		
		return $this;
	}
	
	public function loadMessageSources()
	{
		$this->i18n->getMessageSource()->setCulture($this->culture);
    $this->i18n->getMessageSource()->load('itSs');
	}
	
	public function saveNewMessages()
	{
		if($this->parameters['module'] instanceof dmProjectModule)
		{
			$path = dmOs::join(sfConfig::get('sf_root_dir'), 'data', 'dm', 'i18n', 'en_' . $this->culture . '.yml');
		}elseif($this->parameters['module']->isPlugin()){
			$pluginPath = sfContext::getInstance()->getConfiguration()->getPluginConfiguration($this->parameters['module']->getPluginName())->getRootDir();
			$path = dmOs::join($pluginPath, 'data', 'dm', 'i18n', 'en_' . $this->culture . '.yml');
		}
		$this->i18n->getMessageSource()->setYaml($path);
		foreach($this->getNewMessages() as $message)
		{
			$this->i18n->getMessageSource()->append($message);
		}
		$this->i18n->getMessageSource()->save();
	}
}