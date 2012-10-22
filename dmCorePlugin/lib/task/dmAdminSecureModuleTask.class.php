<?php

class dmAdminSecureModuleTask extends dmContextTask
{
	public function configure()
	{
		parent::configure();

		$this->addOptions(array(
		new sfCommandOption('all', null, sfCommandOption::PARAMETER_NONE, 'Re-generate for all modules')
		));
		
		$this->addArgument('module', sfCommandArgument::OPTIONAL, 'Module key', null);


		$this->aliases = array();
		$this->namespace = 'dmAdmin';
		$this->name = 'secure-module';
		$this->briefDescription = 'Generates a security.yml for module using modules.yml definition';

	}

	public function execute($arguments = array(), $options = array())
	{
		if(null === $arguments['module'] && !$options['all']) throw new LogicException('You must give a module key or add --all option');
		
		$modules = $options['all'] ? 
		$this->get('module_manager')->getModules() : 
		array($this->get('module_manager')->getModule($arguments['module']));
		
		foreach($modules as $moduleObject)
		{
			$moduleObject->getSecurityManager()->secure($moduleObject);
		}
	}
}