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
    $routing = dmContext::getInstance()->getRouting();
    
    foreach(dmContext::getInstance()->getModuleManager()->getModules() as $module)
    {
      if (!$routing->hasRouteName($module->getKey()))
      {
        continue;
      }
      
      $moduleUrl = $routing->generate($module->getKey());
      
      $urls[] = $moduleUrl;
      
      if ($module->hasModel() && $module->getTable()->hasField('id'))
      {
        $records = $module->getTable()->createQuery('t')->orderBy('RAND()')->limit(1)->select('id')->fetchArray();
        
        foreach($records as $record)
        {
          $urls[] = $moduleUrl.'/'.$record['id'];
        }
      }
    }
    
    return $urls;
  }
}