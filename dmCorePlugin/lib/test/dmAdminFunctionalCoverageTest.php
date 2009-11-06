<?php

class dmAdminFunctionalCoverageTest extends dmCoreFunctionalCoverageTest
{
  
  protected function configure()
  {
    parent::configure();
    
    if (empty($this->options['app']))
    {
      $this->options['app'] = 'admin';
    }
  }
  
  protected function execute()
  {
    foreach($this->getUrls() as $url)
    {
      $this->testUrl($url);
    }
  }
  
  protected function getUrls()
  {
    $urls = array('/');
    $routing = $this->context->getRouting();
    
    foreach($this->context->getModuleManager()->getModules() as $module)
    {
      if (!$routing->hasRouteName($module->getUnderscore()))
      {
        continue;
      }
      
      $moduleUrl = $routing->generate($module->getUnderscore());
      
      $urls[] = $moduleUrl;
      
      if ($module->hasModel() && $module->getTable()->hasField('id'))
      {
        $records = $module->getTable()->createQuery('t')->orderBy('RAND()')->limit(1)->select('t.id')->fetchArray();
        
        foreach($records as $record)
        {
          $urls[] = $moduleUrl.'/edit/pk/'.$record['id'];
        }
      }
    }
    
    $uriPrefixLength = strlen($this->context->getRequest()->getUriPrefix());
    foreach($urls as $index => $url)
    {
      $urls[$index] = '/'.substr($url, $uriPrefixLength);
    }
    
    return $urls;
  }
}