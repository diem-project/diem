<?php

class dmCacheCleaner
{
  protected
  $cacheManager,
  $dispatcher,
  $options,
  $queue;
  
  const
  TEMPLATE = 'template';
  
  public function __construct(dmCacheManager $cacheManager, sfEventDispatcher $dispatcher, array $options = array())
  {
    $this->cacheManager = $cacheManager;
    $this->dispatcher   = $dispatcher;
    
    $this->initialize($options);
  }
  
  protected function initialize(array $options = array())
  {
    $this->options = array_merge($this->getDefaultOptions(), $options);
    
    $this->queue = array();
  }
  
  protected function getDefaultOptions()
  {
    return array(
      'safe_models' => array('DmSentMail', 'DmError', 'DmRedirect')
    );
  }
  
  public function connect()
  {
    $this->dispatcher->connect('dm.controller.redirect', array($this, 'listenToControllerRedirectionEvent'));
    
    $this->dispatcher->connect('dm.record.modification', array($this, 'listenToRecordModificationEvent'));
  }
  
  public function addToQueue($cachePart)
  {
    $this->queue[] = $cachePart;
  }
  
  public function listenToRecordModificationEvent(sfEvent $event)
  {
    $model = get_class($event->getSubject());
    
    if (!in_array($model, $this->options['safe_models']))
    {
      $this->addToQueue(self::TEMPLATE);
    }
  }
  
  public function listenToControllerRedirectionEvent(sfEvent $event)
  {
    $this->processQueue();
  }
  
  public function processQueue()
  {
    $queue = array_unique($this->queue);
    
    foreach($queue as $cachePart)
    {
      switch($cachePart)
      {
        case self::TEMPLATE:
          $this->clearTemplate();
          break;
        default:
          throw new dmException(sprintf('%s is not a valid cachePart.', $cachePart));
      }
    }
  }
  
  public function clearTemplate()
  {
    foreach($this->options['applications'] as $app)
    {
      foreach($this->options['environments'] as $env)
      {
        $this->cacheManager->getCache(sprintf('%s/%s/template', $app, $env))->clear();
      }
    }
  }
  
}