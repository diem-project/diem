<?php

class dmWebBrowser extends sfWebBrowser
{
  
  public function __construct($defaultHeaders = array(), $adapterClass = null, $adapterOptions = array())
  {
    parent::__construct($defaultHeaders, $adapterClass, $adapterOptions);

    $this->setUserAgent(sprintf('%s (%s) Diem/%s (%s)',
      dmConfig::get('site_name'),
      dmArray::get($_SERVER, 'HTTP_HOST', '?'),
      DIEM_VERSION,
      'http://diem-project.org'
    ));
  }
}