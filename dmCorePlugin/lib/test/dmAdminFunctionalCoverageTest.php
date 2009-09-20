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
    foreach(dmModuleManager::getModules() as $module)
    {
      if (!$module->getDir())
      {
        continue;
      }
      
      $moduleUrl = '/'.$module->getCompleteSlug();
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