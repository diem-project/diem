<?php

class dmSearchEngine extends dmSearchIndexGroup
{
  protected
  $user,
  $serviceContainer;
  
  public function __construct(sfEventDispatcher $dispatcher, dmUser $user, sfLogger $logger, sfServiceContainer $serviceContainer)
  {
    $this->dispatcher       = $dispatcher;
    $this->user             = $user;
    $this->logger           = $logger;
    $this->serviceContainer = $serviceContainer;
    $this->name             = get_class($this);
  }
  
  public function setLogger(sfLogger $logger)
  {
    $this->logger = $logger;
  }
  
  protected function configure()
  {
    foreach(sfConfig::get('dm_i18n_cultures') as $culture)
    {
      $index = $this->serviceContainer->getService('search_index');
      $index->setCulture($culture);
      $index->setLogger($this->logger);
      $this->addIndex($index->getName(), $index);
    }
  }
  
  public function insert(DmPage $page)
  {
    $this->setup();

    foreach ($this->getIndices() as $index)
    {
      $index->insert($page);
    }
  }

  public function remove(DmPage $page)
  {
    $this->setup();

    foreach ($this->getIndices() as $index)
    {
      $index->remove($page);
    }
  }
  
  public function refresh(DmPage $page)
  {
    $this->remove($page);
    $this->insert($page);
  }
  
  public function search($query)
  {
    return $this->getCurrentIndex()->search($query);
  }
  
  public function getCurrentIndex()
  {
    return $this->getIndex('dm_'.$this->user->getCulture());
  }
  
  public function populate(dmContext $context)
  {
    $this->setup();

    $start = microtime(true);

    $this->logger->log($this->getName().' : Populating group...');
    
    $oldCulture = $this->user->getCulture();
    
    foreach ($this->getIndices() as $name => $index)
    {
      $this->logger->log($this->getName().' : Populating index "' . $name . '"...');

      $this->user->setCulture($index->getCulture());
      
      $index->populate($context);
    }
    
    $this->user->setCulture($oldCulture);

    $this->logger->log($this->getName().' : Group populated in "' . round(microtime(true) - $start, 2) . '" seconds.');
  
    $this->logger->log('-----> Search index population successfully completed');
    
    return true;
  }
  
  public function optimize()
  {
    $this->setup();

    $start = microtime(true);

    $this->logger->log($this->getName().' : Optimizing group...');
    
    foreach($this->getIndices() as $index)
    {
      $index->optimize();
    }

    $this->logger->log($this->getName().' : Group optimized in "' . round(microtime(true) - $start, 2) . '" seconds.');
    
    return true;
  }
  
  /**
   * @see xfIndex
   */
  public function describe()
  {
    $this->setup();

    $response = array();

    foreach ($this->getIndices() as $name => $index)
    {
      $response[$index->getCulture()] = $index->describe();
    }

    return $response;
  }
}