<?php
/**
 */
class PluginDmPageTable extends myDoctrineTable
{
  protected
  $recordPageCache = array(),
  $findByStringCache = array();
  
  /**
   * Check that basic pages exist
   * ( main/root, main/error404, main/signin )
   * and, if they don't, will create them
   */
  public function checkBasicPages()
  {
    // check root page
    if (!$root = $this->getTree()->fetchRoot())
    {
      $root = $this->create(array(
        'module' => 'main',
        'action' => 'root',
        'name' => $this->tryToTranslate('Home'),
        'title' => $this->tryToTranslate('Home'),
        'slug' => ''
      ));

      $this->getTree()->createRoot($root);
      
      if ($layout = dmDb::table('DmLayout')->findOneByName('Home'))
      {
        $root->get('PageView')->set('Layout', $layout)->save();
      }
    }

    // check error404 page
    if (!$this->createQuery('p')->where('p.module = ? AND p.action = ?', array('main', 'error404'))->exists())
    {
      $page404 = $this->create(array(
        'module' => 'main',
        'action' => 'error404',
        'name' => $this->tryToTranslate('Page not found'),
        'title' => $this->tryToTranslate('Page not found'),
        'slug' => 'error404'
      ));

      $page404->getNode()->insertAsLastChildOf($root);

      dmDb::table('DmWidget')->createInZone(
        $page404->get('PageView')->get('Area')->get('Zones')->getFirst(),
        'dmWidgetContent/title',
        array('text' => 'Page not found', 'tag' => 'h1')
      )->save();
    }

    // check signin page
    if (!$this->createQuery('p')->where('p.module = ? AND p.action = ?', array('main', 'signin'))->exists())
    {
      $signinPage = $this->create(array(
        'module' => 'main',
        'action' => 'signin',
        'name' => $this->tryToTranslate('Signin'),
        'title' => $this->tryToTranslate('Signin'),
        'slug' => 'security/signin'
      ));
      
      $signinPage->getNode()->insertAsLastChildOf($root);

      dmDb::table('DmWidget')->createInZone(
        $signinPage->get('PageView')->get('Area')->get('Zones')->getFirst(),
        'dmUser/signin'
      )->save();
    }
  }

  protected function tryToTranslate($message)
  {
    if($i18n = $this->getService('i18n'))
    {
      return $i18n->__($message);
    }

    return $message;
  }
  
  /**
   * Check that search page exist
   * and, if doesn't, will create it
   */
  public function checkSearchPage()
  {
    if (!$this->createQuery('p')->where('p.module = ? AND p.action = ?', array('main', 'search'))->exists())
    {
      $searchResultsPage = $this->create(array(
        'name' => $this->tryToTranslate('Search results'),
        'title' => $this->tryToTranslate('Search results'),
        'module' => 'main',
        'action' => 'search',
        'slug' => 'search'
      ));

      $searchResultsPage->getNode()->insertAsLastChildOf($this->getTree()->fetchRoot());

      dmDb::table('DmWidget')->createInZone(
        $searchResultsPage->get('PageView')->get('Area')->get('Zones')->getFirst(),
        'dmWidgetSearch/results'
      )->save();
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
    ->select('p.id, p.module, p.action, p.record_id, pTranslation.is_secure, p.lft, p.rgt, pTranslation.slug, pTranslation.name, pTranslation.title, pTranslation.is_active')
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
  }

  public function isSlugUnique($slug, $id)
  {
    return !$this->getI18nTable()->createQuery('pt')
    ->where('pt.lang = ?', dmDoctrineRecord::getDefaultCulture())
    ->andwhere('pt.id != ?', $id ? $id : 0)
    ->andWhere('pt.slug = ?', $slug)
    ->exists();
  }

  public function createUniqueSlug($slug, $id, $parentSlug = null)
  {
    if(null === $parentSlug)
    {
      $parentSlug = $this->getI18nTable()->createQuery('pt')
      ->where('pt.id = ?', $this->findOneById($id)->getNodeParentId())
      ->andWhere('pt.lang = ?', dmDoctrineRecord::getDefaultCulture())
      ->select('pt.slug')
      ->fetchValue();
    }
    
    if($slug == $parentSlug)
    {
      $slug .= '/'.$id;
    }
    else
    {
      $slug .= '-'.$id;
    }
    
    return $slug;
  }
  
  /**
   * Queries
   */

  public function queryByModuleAndAction($module, $action)
  {
    return $this->createQuery('p')
    ->where('p.module = ? AND p.action = ?', array($module, $action));
  }

  
  public function findAllForCulture($culture, $hydrationMode = Doctrine_Core::HYDRATE_ARRAY)
  {
    return $this->createQuery('p')
    ->withI18n($culture, null, 'p')
    ->execute(array(), $hydrationMode);
  }
  
  /**
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
          $source = substr($source, 0, $anchorPos);
        }
        if ($spacePos = strpos($source, ' '))
        {
          $source = substr($source, 0, $spacePos);
        }
        
        if (strncmp($source, 'page:', 5) === 0)
        {
          $this->findByStringCache[$source] = $this->findOneByIdWithI18n((int)substr($source, 5));
        }
        elseif(substr_count($source, '/') === 1)
        {
          $parts = explode('/', $source);
          
          $this->findByStringCache[$source] = $this->findOneByModuleAndActionWithI18n($parts[0], $parts[1]);
        }
        else
        {
          $this->findByStringCache[$source] = null;
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
    ->where('p.module = ?', $record->getDmModule()->getKey())
    ->andWhere('p.action = ?', 'show')
    ->andWhere('p.record_id = ?', $record->get('id'))
    ->fetchRecord();
  }
  
  public function findOneBySlug($slug, $culture = null)
  {
    return $this->createQuery('p')
    ->withI18n($culture, null, 'p', 'inner')
    ->where('pTranslation.slug = ?', $slug)
    ->fetchOne();
  }

  public function findByLevelWithI18n($level, $culture = null)
  {
    return $this->createQuery('p')
    ->where('p.level = ?', $level)
    ->withI18n($culture, null, 'p')
    ->fetchRecords();
  }

  public function fetchError404()
  {
    $this->checkBasicPages();
    
    return $this->findOneByModuleAndActionWithI18n('main', 'error404');
  }
  
  public function fetchSignin()
  {
    $this->checkBasicPages();
    
    return $this->findOneByModuleAndActionWithI18n('main', 'signin');
  }

  public function findOneById($id)
  {
    return $this->createQuery('p')
    ->where('p.id = ?', $id)
    ->fetchOne();
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
    ->fetchOne();
  }

  public function findByModuleAndAction($module, $action)
  {
    return $this->createQuery('p')
    ->where('p.module = ?', $module)
    ->andWhere('p.action = ?', $action)
    ->fetchRecords();
  }
  
  public function findByModuleAndActionWithI18n($module, $action)
  {
  	return $this->createQuery('p')
    ->withI18n()
    ->where('p.module = ?', $module)
    ->andWhere('p.action = ?', $action)
    ->fetchRecords();
  }

  public function findOneByModuleAndAction($module, $action)
  {
    return $this->createQuery('p')
    ->where('p.module = ?', $module)
    ->andWhere('p.action = ?', $action)
    ->fetchRecord();
  }

  public function findOneByModuleAndActionWithI18n($module, $action, $culture = null)
  {
    return $this->createQuery('p')
    ->where('p.module = ?', $module)
    ->andWhere('p.action = ?', $action)
    ->withI18n($culture, null, 'p')
    ->fetchOne();
  }

}