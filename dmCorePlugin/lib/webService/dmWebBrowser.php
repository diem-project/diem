<?php

class dmWebBrowser extends sfWebBrowser
{
  public function __construct($userAgent = null)
  {
    parent::__construct(array(), null, array());

    $this->setUserAgent(
      dmConfig::get('site_name').
      " (".dm::getRequest()->getAbsoluteUrlRoot().")".
      " powered by Diem/".dm::version().
      " (http://diem.iliaz.com)".
      ($userAgent ? " - ".$userAgent : "")
    );
  }
}