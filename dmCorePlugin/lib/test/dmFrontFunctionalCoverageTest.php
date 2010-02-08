<?php

require_once dirname(__FILE__).'/dmCoreFunctionalCoverageTest.php';

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
      if ($page->isModuleAction('main', 'error404'))
      {
        $expectedStatusCode = 404;
      }
      elseif(($page->get('is_secure') || !$page->get('is_active')) && !$this->browser->getContext()->getUser()->isAuthenticated())
      {
        var_dump($page->Translation['en']->data);
        $expectedStatusCode = 401;
      }
      else
      {
        $expectedStatusCode = 200;
      }
      
      $this->testUrl('/'.$page->slug, $expectedStatusCode);

      if($this->willRunOutOfMemory())
      {
        $this->browser->info('Stop before memory limit is reached');
        break;
      }
    }
  }
  
  protected function getPages()
  {
    return dmDb::query('DmPage p')
    ->withI18n()
    ->fetchRecords();
  }
}