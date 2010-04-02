<?php

class dmFunctionalTestHelper
{
  protected
  $configuration,
  $context,
  $browser,
  $contextClass;

  public function __construct($contextClass = 'dmContext')
  {
    $this->contextClass = $contextClass;
  }

	public function boot($app = 'admin', $env = 'test', $debug = true)
	{
    $rootDir = getcwd();

    // configuration
    require_once $rootDir.'/config/ProjectConfiguration.class.php';
		$this->configuration = ProjectConfiguration::getApplicationConfiguration($app, $env, $debug, $rootDir);
		
		$this->context = dmContext::createInstance($this->configuration, null, $this->contextClass);

    sfConfig::set('sf_logging_enabled', false);

    // remove all cache
    sfToolkit::clearDirectory(sfConfig::get('sf_app_cache_dir'));

    $this->cleanup();

    $this->initialize();

    register_shutdown_function(array($this, 'cleanup'));

    return $this;
	}

  protected function initialize()
  {
    $this->browser = $this->context->get('test_functional', 'front' == sfConfig::get('sf_app') ? 'dmFrontTestFunctional' : null);
    $this->browser->initialize();
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

//      $this->context->get('filesystem')->
    }
    catch (Exception $e)
    {
      echo $e.PHP_EOL;
    }
  }
  
  public function login($username = 'admin', $password = 'admin')
  {
    return 'front' === sfConfig::get('sf_app')
    ? $this->frontLogin($username, $password)
    : $this->adminLogin($username, $password);
  }

  protected function adminLogin($username, $password)
  {
    $this->browser->get('/security/signin?skip_browser_detection=1')
    ->checks(array(
      'code' => 401,
      'moduleAction' => 'dmUserAdmin/signin'
    ))
    ->click('input[type="submit"]',
      array('signin' => array('username' => $username, 'password' => $password)),
      array('method' => 'post', '_with_csrf' => true)
    )
    ->checks(array(
      'code' => 302,
      'moduleAction' => 'dmUserAdmin/signin'
    ))
    ->redirect();

    return $this->browser->with('user')->begin()->isAuthenticated(true)->end();
  }

  protected function frontLogin($username, $password)
  {
    $this->browser->get('/security/signin')
    ->checks(array(
      'code' => 200,
      'moduleAction' => 'dmFront/page'
    ))
    ->isPageModuleAction('main/signin')
    ->click('Signin',
      array('signin' => array('username' => $username, 'password' => $password)),
      array('method' => 'post', '_with_csrf' => true)
    )
    ->checks(array(
      'code' => 302,
      'moduleAction' => 'dmFront/page'
    ))
    ->redirect();

    return $this->browser->with('user')->begin()->isAuthenticated(true)->end();
  }

  public function logout()
  {
    $this->browser->get('/security/signout')
    ->checks(array(
      'code' => 302,
      'moduleAction' => 'front' === sfConfig::get('sf_app') ? 'dmUser/signout' : 'dmUserAdmin/signout'
    ))
    ->redirect();

    return $this->browser->with('user')->begin()->isAuthenticated(false)->end();
  }

  public function getService($name)
  {
    return $this->context->get($name);
  }
}