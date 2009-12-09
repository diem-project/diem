<?php

require_once(getcwd().'/test/bootstrap/unit.php');

class dmFunctionalTestHelper
{

	public function boot($app = 'admin', $env = 'test', $debug = true)
	{
		$appConfig = ProjectConfiguration::getApplicationConfiguration($app, $env, $debug, null, new sfEventDispatcher());
		
		$this->context = dmContext::createInstance($appConfig);

    $this->initialize();

    return $this;
	}
}