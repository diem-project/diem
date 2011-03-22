<?php

class dmI18nExtractPluginTask extends dmContextTask
{
	protected function configure()
	{
		parent::configure();
			
		$this->addArgument('culture', sfCommandArgument::REQUIRED, 'Specify culture');
		$this->addArgument('plugins', sfCommandArgument::IS_ARRAY, 'Specify plugins');
		$this->addOption('source', 's', sfCommandArgument::OPTIONAL, 'Specify source', 'dm');
			
		$this->namespace        = 'dm';
		$this->name             = 'i18n-plugin-extract';
		$this->briefDescription = 'Extract I18n strings into data/dm/i18n/files of plugins';
		$this->detailedDescription = <<<EOF
The [dmI18nExtract|INFO] task does things.
Call it with:

  [php symfony dmI18nExtract|INFO]
EOF;
	}

	protected function execute($arguments = array(), $options = array())
	{
		// get i18n configuration from factories.yml
		$config = sfFactoryConfigHandler::getConfiguration($this->configuration->getConfigPaths('config/factories.yml'));

		$class = $config['i18n']['class'];
		$params = $config['i18n']['param'];
		$params['source'] = $options['source'];
		unset($params['cache']);
		
		$moduleManager = $this->getContext()->getServiceContainer()->getService('module_manager');
		$modules = $moduleManager->getModules();

		$this->extractObjects = array();

		foreach($arguments['plugins'] as $pluginName)
		{
			$pluginObject = $this->getContext()->getConfiguration()->getPluginConfiguration($pluginName);
			$pluginExtractor = new dmI18nPluginExtract(new $class($this->configuration, new sfNoCache(), $params), $arguments['culture'], array('plugin' => $pluginObject, 'serviceContainer' => $this->getContext()->getServiceContainer(), 'logSection' => new sfCallable(array($this, 'logSection'))));
			$this->extractObjects[] = $pluginExtractor->extract();
		}

		foreach($this->extractObjects as $extract)
		{
			$extract->saveNewMessages();
		}
	}
	
	public function logSection($section, $message, $size = null, $style = 'INFO')
	{
		parent::logSection($section, $message, $size, $style);
	}
}
