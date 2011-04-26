<?php

class dmI18nYamlExtractor
{
  protected
  $i18n,
  $configuration,
  $dispatcher,
  $formatter;

  public function __construct(dmI18n $i18n, sfApplicationConfiguration $configuration, sfEventDispatcher $dispatcher)
  {
    $this->i18n           = $i18n;
    $this->configuration  = $configuration;
    $this->dispatcher     = $dispatcher;

    $this->initialize();
  }

  protected function initialize()
  {
    
  }

  public function setFormatter(sfFormatter $formatter)
  {
    $this->formatter = $formatter;
  }

  public function execute()
  {
    
  }

  protected function log($message, $size = null, $style = 'INFO')
  {
    $message = $this->formatter
    ? $this->formatter->formatSection('i18n_yaml_extractor', $message, $size, $style)
    : $message;
    
    $this->dispatcher->notify(new sfEvent($this, 'command.log', array($message)));
  }
}