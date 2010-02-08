<?php

require_once dirname(__FILE__).'/dmCoreFunctionalCoverageTest.php';

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

      if($this->willRunOutOfMemory())
      {
        $this->browser->info('Stop before memory limit is reached');
        break;
      }
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
        $records = $module->getTable()->createQuery('t')
        ->select('t.id, RANDOM() AS rand')
        ->orderBy('rand')
        ->limit(2)
        ->fetchArray();
        
        foreach($records as $record)
        {
          $urls[] = $moduleUrl.'/edit/pk/'.$record['id'];
        }
      }
    }

    foreach($this->context->getModuleManager()->getTypes() as $type)
    {
      $urls[] = $this->context->getController()->genUrl($routing->getModuleTypeUrl($type));

      foreach($type->getSpaces() as $space)
      {
        $urls[] = $this->context->getController()->genUrl($routing->getModuleSpaceUrl($space));
      }
    }

    $uriPrefixLength = strlen(dmArray::get(dmArray::get($routing->getOptions(), 'context'), 'prefix'));
    
    foreach($urls as $index => $url)
    {
      $urls[$index] = substr($url, $uriPrefixLength);
    }
    
    return $urls;
  }
}