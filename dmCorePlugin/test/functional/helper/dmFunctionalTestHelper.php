<?php

class dmFunctionalTestHelper
{
  protected
  $configuration,
  $context,
  $browser;

	public function boot($app = 'admin', $env = 'test', $debug = true)
	{
    $rootDir = getcwd();

    // configuration
    require_once $rootDir.'/config/ProjectConfiguration.class.php';
		$this->configuration = ProjectConfiguration::getApplicationConfiguration($app, $env, $debug, $rootDir);
		
		$this->context = dmContext::createInstance($this->configuration);

    sfConfig::set('sf_logging_enabled', false);

    // remove all cache
    sfToolkit::clearDirectory(sfConfig::get('sf_app_cache_dir'));

    $this->initialize();

    register_shutdown_function(array($this, 'cleanup'));

    $this->cleanup();

    return $this;
	}

  protected function initialize()
  {
    $this->browser = $this->context->get('test_functional');
    $this->browser->initialize();

    $this->browser->info('Running dm:publish-assets');
    $task = new dmPublishAssetsTask($this->configuration->getEventDispatcher(), new sfFormatter());
    $task->run();
  }

  public function getBrowser()
  {
    return $this->browser;
  }

  function cleanup()
  {
    // try/catch needed due to http://bugs.php.net/bug.php?id=33598
    try
    {
      if(method_exists($this->configuration, 'cleanup'))
      {
        $this->configuration->cleanup($this->context->get('filesystem'));
      }
    }
    catch (Exception $e)
    {
      echo $e.PHP_EOL;
    }
  }
  
  public function login($username = 'admin', $password = 'admin')
  {
    $url = ('front' === sfConfig::get('sf_app'))
    ? '/login'
    : '/+/dmAuth/signin?skip_browser_detection=1';

    $this->browser->
      get($url)->
      click('input[type="submit"]',
        array('signin' => array('username' => $username, 'password' => $password)),
        array('method' => 'post', '_with_csrf' => true)
      )->
      with('response')->begin()->
        followRedirect()->
      end()
    ;

    $this->browser->with('user')->begin()->isAuthenticated()->end();

    return $this;
  }

  public function logout()
  {
    return $this->browser->
      get('/+/dmAuth/signout')->
      with('response')->begin()->
        followRedirect()->
      end()
    ;

    $this->browser->with('user')->begin()->isAuthenticated(false)->end();
  }
}