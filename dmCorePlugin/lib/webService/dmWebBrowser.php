<?php

class dmWebBrowser extends sfWebBrowser
{

  public function getDefaultOptions()
  {
    return array(
      'default_headers' => array(),
      'adapter_class'   => null,
      'adapter_options' => array()
    );
  }
  
  public function __construct(array $options)
  {
    $options = array_merge($this->getDefaultOptions(), $options);
    
    parent::__construct($options['default_headers'], $options['adapter_class'], $options['adapter_options']);

    $this->setUserAgent(sprintf('%s (%s) Diem/%s (%s)',
      dmConfig::get('site_name'),
      dmArray::get($_SERVER, 'HTTP_HOST', '?'),
      DIEM_VERSION,
      'http://diem-project.org'
    ));
  }
}