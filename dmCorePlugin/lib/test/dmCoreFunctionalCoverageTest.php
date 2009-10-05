<?php

abstract class dmCoreFunctionalCoverageTest
{
  protected
  $options,
  $browser,
  $stats,
  $counter;

  protected static $defaults = array(
    'env'    => 'test',
    'debug'  => false,
    'login'  => false,
    'maxRedirections' => 5
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

    dm::createContext($configuration);

    // remove all cache
    sfToolkit::clearDirectory(sfConfig::get('sf_app_cache_dir'));
  }

  protected function initBrowser()
  {
    $this->browser = new sfTestFunctional(new dmTestBrowser());
    
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
    if (empty($this->options['username']) || empty($this->options['password']))
    {
      throw new dmException('You must provide a username and a password to login');
    }
    
    $form = new sfGuardFormSignin;
    
    $form->bind(array(
      'username' => $this->options['username'],
      'password' => $this->options['password']
    ));
    
    if (!$form->isValid())
    {
      throw new dmException('Can not login : bad username / password');
    }

    $this->browser->info('Login as '.$this->options['username'])
    ->get('/+/dmAuth/signin')
    ->setField('signin[username]', $this->options['username'])
    ->setField('signin[password]', $this->options['password'])
    ->click(dm::getI18n()->__('Login'))
    ->isRedirected()
    ->followRedirect();
    
    while(in_array($this->browser->getResponse()->getStatusCode(), array(301, 302)))
    {
      $this->browser->isRedirected()->followRedirect();
    }
    
    $this->browser->with('response')->begin()
    ->isStatusCode(200)
    ->end();
  }

  protected function testUrl($url, $expectedStatusCode = 200)
  {
    $nbRedirects = 0;

    $this->startCounter($url);
    
    dm::resetStartTime();

    $this->browser->get($url);

    while(in_array($this->browser->getResponse()->getStatusCode(), array(301, 302)))
    {
      $this->browser->isRedirected()->followRedirect();
      
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
    $mem = sprintf('%01.3f', (memory_get_usage() - $this->counter['mem'])/1024);
     
    $this->browser->info(round($time).' ms | '.round($mem).' Ko');

    if ($time > $this->stats['maxTime']['value'])
    {
      $this->stats['maxTime'] = array('url' => $this->counter['url'], 'value' => $time);
    }
    if ($mem > $this->stats['maxMem']['value'])
    {
      $this->stats['maxMem'] = array('url' => $this->counter['url'], 'value' => $mem);
    }

    $this->stats['totalTime'] += $time;
    $this->stats['totalMem'] += $mem;
    $this->stats['nbUrls'] += 1;
  }
  
  protected function showStats()
  {
    $averageTime  = $this->stats['totalTime'] / $this->stats['nbUrls'];
    $averageMem   = $this->stats['totalMem'] / $this->stats['nbUrls'];
    
    $this->browser->info('------------------------------------------------');
    
    $this->browser->info(sprintf('Average time : %01.3f ms', $averageTime));
    $this->browser->info(sprintf('Average memory : %01.3f Ko', $averageMem));
    
    $this->browser->info(sprintf('Max time : %01.3f ms on %s', $this->stats['maxTime']['value'], $this->stats['maxTime']['url']));
    $this->browser->info(sprintf('Max memory : %01.3f Ko on %s', $this->stats['maxMem']['value'], $this->stats['maxMem']['url']));
  }
}