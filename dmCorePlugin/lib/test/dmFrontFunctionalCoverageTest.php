<?php

class dmFrontFunctionalCoverageTest extends dmCoreFunctionalCoverageTest
{
  
  protected function configure()
  {
    parent::configure();
    
    if (empty($this->options['app']))
    {
      $this->options['app'] = 'front';
    }
  }
  
  protected function execute()
  {
    foreach($this->getPages() as $page)
    {
      $expectedStatusCode = $page->isModuleAction('main', 'error404') ? 404 : 200;
      
      $this->testUrl($page->slug, $expectedStatusCode);
    }
  }
  
  protected function getPages()
  {
    return dmDb::table('DmPage')->findAll();
  }
}