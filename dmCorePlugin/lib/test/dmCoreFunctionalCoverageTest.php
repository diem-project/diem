<?php

abstract class dmCoreFunctionalCoverageTest
{
  protected
  $context,
  $options,
  $browser,
  $stats,
  $counter;

  protected static $defaults = array(
    'env'    => 'test',
    'debug'  => false,
    'login'  => false,
    'maxRedirections' => 5,
    'validate' => true
  );

  public function __construct(array $options)
  {
    $this->options = array_merge(self::$defaults, $options);
    $this->configure();
  }

  protected function configure()
  {
  }

  abstract protected function execute();

  public function run()
  {
    $this->boot();

    $this->initBrowser();

    $this->initStats();

    if ($this->options['login'])
    {
      $this->login();
    }
    /*
     * Preload cache to ensure stats consistency
     */
    $this->browser->get('');

    $this->execute();
    
    $this->showStats();
  }

  protected function boot()
  {
    $configuration = ProjectConfiguration::getApplicationConfiguration($this->options['app'], $this->options['env'], $this->options['debug']);

    $this->context = dm::createContext($configuration);
    
    sfConfig::set('sf_logging_enabled', false);

    // remove all cache
    sfToolkit::clearDirectory(sfConfig::get('sf_app_cache_dir'));
  }

  protected function initBrowser()
  {
    $this->browser = $this->context->get('test_functional');
    
    if($this->options['debug'])
    {
      $this->browser->info('If allowed memory size is exhausted, try setting debug to off');
    }
  }

  protected function initStats()
  {
    $this->stats = array(
      'nbUrls'    => 0,
      'maxTime'   => array('url' => '?', 'value' => 0),
      'maxMem'    => array('url' => '?', 'value' => 0),
      'totalTime' => 0,
      'totalMem'  => 0
    );
  }

  protected function login()
  {
    $user = dmDb::table('DmUser')->findOneByUsername($this->options['username']);
    
    if(!$user instanceof DmUser)
    {
      throw new dmException(sprintf(
        'The "%s" user does not exist. Please provide a valid username in your functional test, or disable login',
        $this->options['username']
      ));
    }
    
    $this->browser->getContext()->getUser()->signin($user);
    
    // make the login persistent
    $this->browser->getContext()->getUser()->shutdown();
    $this->browser->getContext()->getStorage()->shutdown();
    
    $this->browser->with('user')->begin()->isAuthenticated()->end();
  }

  protected function testUrl($url, $expectedStatusCode = 200)
  {
    $nbRedirects = 0;

    $this->startCounter($url);
    
    dm::resetStartTime();
    
    $this->browser->get($url);

    while(in_array($this->browser->getResponse()->getStatusCode(), array(301, 302)))
    {
      $this->browser->with('response')->begin()->isRedirected()->end()->followRedirect();
      
      $nbRedirects++;
      if ($nbRedirects > $this->options['maxRedirections'])
      {
        $this->browser->info('Too many redirections');
        break;
      }
    }

    $this->stopCounter();

    $this->browser->with('response')->begin()
    ->isStatusCode($expectedStatusCode)
    ->end();
    
    if ($this->options['validate'])
    {
      $this->browser->with('response')->begin()->isValid()->end();
    }
  }

  protected function startCounter($url)
  {
    $this->counter = array(
      'url'   => $url,
      'mem'   => memory_get_usage(),
      'time'  => microtime(true)
    );
  }

  protected function stopCounter()
  {
    $time = sprintf('%01.3f', 1000 * (microtime(true) - $this->counter['time']));
//    $mem = sprintf('%01.3f', (memory_get_usage() - $this->counter['mem'])/1024);
     
//    $this->browser->info(round($time).' ms | '.round($mem).' Ko');
    $this->browser->info(round($time).' ms');

    if ($time > $this->stats['maxTime']['value'])
    {
      $this->stats['maxTime'] = array('url' => $this->counter['url'], 'value' => $time);
    }
//    if ($mem > $this->stats['maxMem']['value'])
//    {
//      $this->stats['maxMem'] = array('url' => $this->counter['url'], 'value' => $mem);
//    }

    $this->stats['totalTime'] += $time;
//    $this->stats['totalMem'] += $mem;
    $this->stats['nbUrls'] += 1;
  }
  
  protected function showStats()
  {
    $averageTime  = $this->stats['totalTime'] / $this->stats['nbUrls'];
//    $averageMem   = $this->stats['totalMem'] / $this->stats['nbUrls'];
    
    $this->browser->info('------------------------------------------------');
    
    $this->browser->info(sprintf('Average time : %01.3f ms', $averageTime));
//    $this->browser->info(sprintf('Average memory : %01.3f Ko', $averageMem));
    
    $this->browser->info(sprintf('Max time : %01.3f ms on %s', $this->stats['maxTime']['value'], $this->stats['maxTime']['url']));
//    $this->browser->info(sprintf('Max memory : %01.3f Ko on %s', $this->stats['maxMem']['value'], $this->stats['maxMem']['url']));
  }

  protected function willRunOutOfMemory()
  {
    return ini_get('memory_limit') > -1 && (dmString::convertBytes(ini_get('memory_limit')) - memory_get_usage()) < (5 * 1024 * 1024);
  }
}