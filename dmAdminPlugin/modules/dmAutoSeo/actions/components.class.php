<?php

class dmAutoSeoComponents extends dmAdminBaseComponents
{
  
  public function executeVariables()
  {
    $this->module = $this->autoSeo->getTargetDmModule();
    
    $this->modules = array_reverse($this->module->getPath(true));
    
    foreach($this->module->getLocals() as $localModuleKey => $localModule)
    {
      if(!isset($this->modules[$localModuleKey]))
      {
        $this->modules[$localModuleKey] = $localModule;
      }
    }
    
    $this->seoSynchronizer = $this->getService('seo_synchronizer');
  }
  
  public function executePreview()
  {
    $this->module = $this->autoSeo->getTargetDmModule();
    $this->page   = null;
    
    $pageId = dmDb::query('DmPage p')
    ->select('p.id, RANDOM() as rand')
    ->where('p.module = ? AND p.action = ?', array($this->module->getKey(), 'show'))
    ->orderBy('rand')
    ->limit(1)
    ->fetchValue();
    
    if ($pageId && ($this->page = dmDb::table('DmPage')->findOneByIdWithI18n($pageId)))
    {
      try
      {
        $seoSynchronizer = $this->getService('seo_synchronizer');
        
        $seoSynchronizer->setCulture($this->getUser()->getCulture())->setModule($this->page->getRecord()->getDmModule());
        
        $this->metas = $seoSynchronizer->compilePatterns(
          $this->rules,
          $seoSynchronizer->getReplacementsForPatterns($this->module, $this->rules, $this->page->getRecord()),
          $this->page->getNode()->getParent()->get('slug')
        );
      }
      catch(Exception $e)
      {
        $this->getUser()->logError($e->getMessage(), false);
        
        if(sfConfig::get('dm_debug'))
        {
          throw $e;
        }
      }
    }
  }
  
}