<?php
/**
 */
class PluginDmPageTable extends myDoctrineTable
{
  protected
  $recordPageCache = array(),
  $findByStringCache = array();
  
  /*
   * Check that basic pages exist
   * ( root page, 404 page )
   * and, if they don't, will create them
   */
  public function checkBasicPages()
  {
    if (!$root = $this->getTree()->fetchRoot())
    {
      $root = $this->create(array(
        'module' => 'main',
        'action' => 'root',
        'name' => dm::getI18n()->__('Home'),
        'title' => dm::getI18n()->__('Home').' | '.dmConfig::get('site_name'),
        'slug' => ''
      ));

      $this->getTree()->createRoot($root);
      
      if ($layout = dmDb::table('DmLayout')->findOneByName('Home'))
      {
        $root->PageView->Layout = $layout;
        $root->PageView->save();
      }
    }

    if (!$this->createQuery('p')->where('p.module = ? AND p.action = ?', array('main', 'error404'))->exists())
    {
      dmDb::create('DmPage', array(
        'module' => 'main',
        'action' => 'error404',
        'name' => dm::getI18n()->__('Page not found'),
        'title' => dm::getI18n()->__('Page not found').' | '.dmConfig::get('site_name'),
        'slug' => '-error404'
      ))->getNode()->insertAsLastChildOf($root);
    }
  }
  
  /*
   * Check that search page exist
   * and, if doesn't, will create them
   */
  public function checkSearchPage()
  {
    if (!$this->createQuery('p')->where('p.module = ? AND p.action = ?', array('main', 'search'))->exists())
    {
      dmDb::create('DmPage', array(
        'name' => dm::getI18n()->__('Search results'),
        'title' => dm::getI18n()->__('Search results').' | '.dmConfig::get('site_name'),
        'module' => 'main',
        'action' => 'search',
        'slug' => '-search'
      ))->getNode()->insertAsLastChildOf($this->getTree()->fetchRoot());
    }
  }

  
  public function preloadPagesForRecords($records)
  {
    if ($records instanceof Doctrine_Collection)
    {
      $records = $records->getData();
    }
    
    foreach($records as $index => $record)
    {
      if (!$record instanceof dmDoctrineRecord)
      {
        unset($records[$index]);
      }
    }
        
    if (!empty($records))
    {
      if (($module = dmArray::first($records)->getDmModule()) && $module->hasPage())
      {
        $ids = array();
        foreach($records as $record)
        {
          $ids[] = $record->get('id');
        }
        
        $this->prepareRecordPageCache($module->getKey(), array_unique($ids));
      }
    }
  }
  
  public function prepareRecordPageCache($module, array $ids, $culture = null)
  {
    $timer = dmDebug::timerOrNull('DmPageTable::prepareRecordPageCache');
    
    if(!empty($this->recordPageCache[$module]))
    {
      foreach($ids as $index => $id)
      {
        if (isset($this->recordPageCache[$module][$id]))
        {
          unset($ids[$index]);
        }
      }
    }
    else
    {
      $this->recordPageCache[$module] = array();
    }
    
    $pages = $this->createQuery('p')
    ->select('p.id, p.module, p.action, p.record_id, p.is_secure, p.lft, p.rgt, pTranslation.slug, pTranslation.name, pTranslation.title, pTranslation.is_active')
    ->where('p.module = ?', $module)
    ->andWhere('p.action = ?', 'show')
    ->andWhereIn('p.record_id', $ids)
    ->withI18n($culture, null, 'p')
    ->fetchRecords()
    ->getData();
    
    foreach($pages as $page)
    {
      $this->recordPageCache[$module][$page->get('record_id')] = $page;
    }
    
    unset($pages);
    
    $timer && $timer->addTime();
  }
  
  /*
   * Queries
   */

  public function queryByModuleAndAction($module, $action)
  {
    return $this->createQuery('p')
    ->where('p.module = ? AND p.action = ?', array($module, $action));
  }

  
  public function findAllForCulture($culture, $hydrationMode = Doctrine::HYDRATE_ARRAY)
  {
    return $this->createQuery('p')
    ->withI18n($culture, null, 'p')
    ->execute(array(), $hydrationMode);
  }
  
  /*
   * Performance finder shortcuts
   */
  
  public function findOneBySource($source)
  {
    if ($source instanceof DmPage)
    {
      return $source;
    }
    elseif($source instanceof myDoctrineRecord)
    {
      return $source->getDmPage();
    }
    if (!isset($this->findByStringCache[$source]))
    {
      if(null === $source)
      {
        $this->findByStringCache[$source] = $this->getTree()->fetchRoot();
      }
      elseif(is_string($source))
      {
        if ($anchorPos = strpos($source, '#'))
        {
          $source = substr($source, $anchorPos);
        }
        if (strncmp($source, 'page:', 5) === 0)
        {
          $this->findByStringCache[$source] = $this->findOneByIdWithI18n(substr($source, 5));
        }
        elseif(substr_count($source, '/') === 1)
        {
          $parts = explode('/', $source);
          
          $this->findByStringCache[$source] = $this->findOneByModuleAndActionWithI18n($parts[0], $parts[1]);
        }
      }
      else
      {
        $this->findByStringCache[$source] = null;
      }
    }
    
    return $this->findByStringCache[$source];
  }

  public function findByAction($action)
  {
    return $this->createQuery('p')->where('p.action = ?', $action)->fetchRecords();
  }

  public function findByModule($module)
  {
    return $this->createQuery('p')->where('p.module = ?', $module)->fetchRecords();
  }

  public function findOneByRecord(myDoctrineRecord $record)
  {
    return $this->createQuery('p')
    ->where('p.module = ? AND p.action = ? AND record_id = ?', array(
      $record->getDmModule()->getKey(), 'show', $record->get('id')
    ))
    ->dmCache()
    ->fetchRecord();
  }
  
  
  public function findOneByIdWithI18n($id, $culture = null)
  {
    return $this->createQuery('p')
    ->where('p.id = ?', $id)
    ->withI18n($culture, null, 'p')
    ->fetchOne();
  }
  
  public function fetchRootWithI18n($culture = null)
  {
    return $this->createQuery('p')
    ->where('p.lft = ?', 1)
    ->withI18n($culture, null, 'p')
    ->fetchOne();
  }
  
  public function findOneByRecordWithI18n(dmDoctrineRecord $record)
  {
    $module = $record->getDmModule()->getKey();
    
    if (isset($this->recordPageCache[$module][$record->get('id')]))
    {
      return $this->recordPageCache[$module][$record->get('id')];
    }

    return $this->createQuery('p')
    ->where('p.module = ?', $module)
    ->andWhere('p.action = ?', 'show')
    ->andWhere('p.record_id = ?', $record->get('id'))
    ->withI18n(null, null, 'p')
    ->dmCache()
    ->fetchOne();
  }

  public function findByModuleAndAction($module, $action)
  {
    return $this->createQuery('p')
    ->where('p.module = ?', $module)
    ->andWhere('p.action = ?', $action)
    ->dmCache()
    ->fetchRecords();
  }

  public function findOneByModuleAndAction($module, $action)
  {
    return $this->createQuery('p')
    ->where('p.module = ?', $module)
    ->andWhere('p.action = ?', $action)
    ->dmCache()
    ->fetchRecord();
  }

  public function findOneByModuleAndActionWithI18n($module, $action, $culture = null)
  {
    $timer = dmDebug::timerOrNull('dmPageTable::findOneByModuleAndActionWithI18n');
    $page = $this->createQuery('p')
    ->where('p.module = ?', $module)
    ->andWhere('p.action = ?', $action)
    ->withI18n($culture, null, 'p')
    ->dmCache()
    ->fetchOne();
    $timer && $timer->addTime();
    return $page;
  }

}