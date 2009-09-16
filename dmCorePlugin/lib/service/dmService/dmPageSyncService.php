<?php

class dmPageSyncService extends dmService
{

  public function execute(array $onlyModules = array())
  {
  	if(empty($onlyModules))
  	{
  		$onlyModules = dmModuleManager::getProjectModules();
  	}
  	
  	$onlyModules = dmModuleManager::removeModulesChildren($onlyModules);
  	
    $timer = dmDebug::timerOrNull('dmPageSyncService::execute');

    dmDB::cache(false);
    sfConfig::set('dm_page_synchronizing', true);

    $this->updateListPages();

    $this->removeShowPages($onlyModules);

    $timer2 = dmDebug::timerOrNull('dmPageSync::updateShowPagesRecursive()');
    $this->updateShowPages($onlyModules);
    $timer2 && $timer2->addTime();

    //    $this->removeShowPages();

    dmDB::cache(true);
    sfConfig::set('dm_page_synchronizing', false);

    $timer && $timer->addTime();
  }

  protected function removeShowPages(array $onlyModules)
  {
    $timer = dmDebug::timerOrNull('dmPageSync : removeShowPages');

    $modulesToCheck = dmDb::query('DmPage p')
    ->select('p.module as mod')
    ->where('p.action = ?', 'show')
    ->distinct()
    ->fetchFlat();

    foreach($onlyModules as $moduleKey => $module)
    {
      $this->removeModuleShowPagesRecursive($module, $modulesToCheck);
    }

    $timer && $timer->addTime();
  }

  protected function removeModuleShowPagesRecursive(dmModule $module, array $modulesToCheck)
  {
    $moduleKey = $module->getKey();

    if (!in_array($moduleKey, $modulesToCheck) || !$module->hasPage())
    {
      foreach($module->getChildren() as $child)
      {
        $this->removeModuleShowPagesRecursive($child, $modulesToCheck);
      }
      return;
    }

    $showPages = dmDb::query('DmPage p')
    ->select('p.module, p.id, p.record_id')
    ->where('p.module = ? AND p.action = ?', array($moduleKey, 'show'))
    ->fetchArray();

    $showPageRecordIds = array();
    foreach($showPages as $showPage)
    {
      $showPageRecordIds[] = $showPage['record_id'];
    }

    $timerDeleteShowPreparation = dmDebug::timerOrNull('delete show preparation');
    if ($module->hasListPage())
    {
      $records = array_flip($module->getTable()->createQuery('r INDEXBY r.id')
      ->select('r.id')
      ->whereIn('r.id', $showPageRecordIds)
      ->fetchFlat());
      $parentModule = $module;
      $parentRecordIds = false;
    }
    else
    {
      $select = 'r.id';
      if ($module->hasLocal($module->getParent()))
      {
        $select .= ', r.'.$module->getTable()->getRelationHolder()->getLocalByClass($module->getParent()->getModel())->getLocal();
      }
      $records = $module->getTable()->createQuery('r INDEXBY r.id')
      ->select($select)
      ->whereIn('r.id', $showPageRecordIds)
      ->fetchArray();
      $parentModule = $module->getNearestAncestorWithPage();
      $parentRecordIds = $this->getParentRecordIds($module, $parentModule, $records);
    }
    $timerDeleteShowPreparation && $timerDeleteShowPreparation->addTime();

    foreach($showPages as $showPage)
    {
      $pageIsUseless = false;

      if(!isset($records[$showPage['record_id']]))
      {
        $pageIsUseless = true;
      }
      elseif(!$module->hasListPage()) // parent page is a show page
      {
      	$record = $records[$showPage['record_id']];
        /*
         * If the parent is a show page, verify that it exists,
         * unless the child page is useless
         */
        if ($parentRecordIds !== false)
        {
          $parentRecordId = isset($parentRecordIds[$record['id']]) ? $parentRecordIds[$record['id']] : null;
        }
        else
        {
          $timerAncestor = dmDebug::timerOrNull('find ancestor');
          $parentRecordId = dmDb::create($module->getModel(), $record)->getAncestorRecordId($parentModule->getModel());
          $timerAncestor && $timerAncestor->addTime();
        }
        if (!$parentRecordId)
        {
          $isUseless = true;
        }
      }

      if ($pageIsUseless)
      {
        dmDb::table('DmPage')->find($showPage['id'])->getNode()->delete();
      }
    }

    foreach($module->getChildren() as $child)
    {
      $this->removeModuleShowPagesRecursive($child, $modulesToCheck);
    }
  }

  protected function updateListPages()
  {
    $timer = dmDebug::timerOrNull('dmPageSync : updateListPages');

    $projectModules = dmModuleManager::getProjectModules();
    foreach($projectModules as $key => $module)
    {
      if(!$module->hasListPage())
      {
        unset($projectModules[$key]);
      }
    }
    $projectModuleKeys = array_keys($projectModules);

    $listPages = dmDb::query('DmPage p INDEXBY p.module')
    ->select('p.id, p.module')
    //    ->whereIn('p.module', array_keys($projectModules))
    ->where('p.action = ?', 'list')
    ->fetchArray();

    $rootPage = dmDb::table('DmPage')->getTree()->fetchRoot();

//    foreach($listPages as $listPage)
//    {
//      if (!in_array($listPage['module'], $projectModuleKeys))
//      {
//        dmDb::table('DmPage')->find($listPage['id'])->getNode()->delete();
//      }
//    }

    foreach($projectModules as $moduleKey => $module)
    {
      /*
       * Only root modules, wich have no parent, need a list page
       */
      if (!isset($listPages[$moduleKey]))
      {
        dmDb::create('DmPage', array(
          'module'      => $moduleKey,
          'action'      => 'list',
          'Translation' => array(
            myDoctrineRecord::getDefaultCulture() => array(
		          'name'        => $module->getPlural(),
		          'title'       => $module->getPlural(),
		          'slug'        => dmString::slugify($module->getPlural()),
		          'description' => $module->getPlural()
            )
          )
        ))->getNode()->insertAsLastChildOf($rootPage);
      }
    }

    $timer && $timer->addTime();
  }

  protected function updateShowPages(array $onlyModules)
  {
    foreach($onlyModules as $moduleKey => $module)
    {
      $this->updateModuleShowPagesRecursive($module);
    }
  }

  protected function updateModuleShowPagesRecursive(dmModule $module)
  {
    $moduleKey = $module->getKey();

    if (!$module->hasPage())
    {
      foreach($module->getChildren() as $child)
      {
        $this->updateModuleShowPagesRecursive($child);
      }

      return;
    }
    
    $timerPreparations = dmDebug::timerOrNull('dmPageSync : preparations');

    /*
     * prepares pages to update
     */
    $timerPreparationsPages = dmDebug::timerOrNull('dmPageSync : preparations - pages');
    $showPages = dmDb::query('DmPage p INDEXBY p.record_id')
    ->where('p.module = ? AND p.action = ?', array($moduleKey, 'show'))
    ->fetchRecords();
    $timerPreparationsPages && $timerPreparationsPages->addTime();

    $pageView = $this->getPageViewForModuleAndAction($moduleKey, 'show');

    if ($module->hasListPage())
    {
      $parentModule = $module;

      /*
       * prepare records
       */
      $timerPreparationsRecords = dmDebug::timerOrNull('dmPageSync : preparations - records');
      $records = $module->getTable()->createQuery('r')->select('r.id')->fetchPDO();
      array_walk($records, create_function('&$a', '$a = array("id" => $a[0]);'));
      $timerPreparationsRecords && $timerPreparationsRecords->addTime();

      /*
       * prepare parent pages
       */
      $parentPageIds = dmDb::table('DmPage')->createQuery('p')
      ->select('p.id')
      ->where('p.module = ? AND p.action = ?', array($moduleKey, 'list'))
      ->fetchValue();
      if (!$parentPageIds)
      {
        throw new dmException(sprintf('%s needs a parent page, %s.%s, but it does not exists', $module, $moduleKey, 'list'));
      }
      $parentRecordIds = false;
    }
    else
    {
      if (!$parentModule = $module->getNearestAncestorWithPage())
      {
        throw new dmException(sprintf(
          '%s module is child of %s module, but %s module has no ancestor with page',
          $module, $parentModule, $module
        ));
      }

      /*
       * prepare records
       */
      $timerPreparationsRecords = dmDebug::timerOrNull('dmPageSync : preparations - records');
      $select = 'r.id';
      if ($module->hasLocal($module->getParent()))
      {
        $select .= ', r.'.$module->getTable()->getRelationHolder()->getLocalByClass($module->getParent()->getModel())->getLocal();
      }
      $records = $module->getTable()->createQuery('r')->select($select)->fetchArray();
      $timerPreparationsRecords && $timerPreparationsRecords->addTime();

      /*
       * prepare parent pages
       */
      $_parentPageIds = dmDb::query('DmPage p')
      ->select('p.id, p.record_id')
      ->where('p.module = ? AND p.action = ?', array($parentModule->getKey(), 'show'))
      ->fetchPDO();

      $parentPageIds = array();
      foreach($_parentPageIds as $value) $parentPageIds[$value[1]] = $value[0];

      $parentRecordIds = $this->getParentRecordIds($module, $parentModule, $records);
    }

    $updatedPages = new myDoctrineCollection('DmPage');

    $timerPreparations && $timerPreparations->addTime();

    foreach($records as $record)
    {
      if (isset($showPages[$record['id']]))
      {
        $page = $showPages[$record['id']];
      }
      else
      {
        $page = dmDb::create('DmPage', array(
          'record_id' => $record['id'],
          'module'    => $module->getKey(),
          'action'    => 'show'
        ));
      }

      try
      {
        $page->setPageView($pageView);

        $this->updatePageFromRecord($page, $record, $module, $parentModule, $parentPageIds, $parentRecordIds);

        $updatedPages[] = $page;
      }
      catch(dmPageMustNotExistException $e)
      {
        if (!$page->isNew())
        {
          $page->getNode()->delete();
        }
      }
    }

    $updatedPages->save();

    foreach($module->getChildren() as $child)
    {
      $this->updateModuleShowPagesRecursive($child);
    }
  }

  public function updatePageFromRecord(DmPage $page, array $record, dmProjectModule $module, dmProjectModule $parentModule, $parentPageIds, $parentRecordIds)
  {
    $timer = dmDebug::timerOrNull('dmPageSync : updatePageFromRecord');

    $moduleKey    = $module->getKey();
    $recordTable  = $module->getTable();
    $pageTable    = dmDb::table('DmPage');

    if ($parentModule->getKey() === $module->getKey()) // parent page is a list page
    {
      $parentPageId = $parentPageIds;
    }
    else // parent page is a show page
    {
      if ($parentRecordIds !== false)
      {
        $parentRecordId = isset($parentRecordIds[$record['id']]) ? $parentRecordIds[$record['id']] : null;
      }
      else
      {
        $parentRecordId = dmDb::create($module->getModel(), $record)->getAncestorRecordId($parentModule->getModel());
      }

      if(!$parentRecordId)
      {
        throw new dmPageMustNotExistException(sprintf('No parent record found for %s %s, page %s must not exist', $module->getModel(), $record, $page));
      }
      elseif (!($parentPageId = dmArray::get($parentPageIds, $parentRecordId)))
      {
        throw new dmPageMustNotExistException(sprintf(
          '%s needs a parent page, %s.%s, but it does not exists for parent object %d',
        $module, $parentModule, 'show', $parentRecordId
        ));
      }
    }

    if ($page->isNew())
    {
      if (!$parentPage = $pageTable->find($parentPageId))
      {
        throw new dmException(sprintf('parent page with id %d for new page %s was not found', $parentPageId, $page));
      }
      $page->getNode()->insertAsLastChildOf($parentPage);
    }
    else
    {
      if ($page->getNodeParentId() != $parentPageId)
      {
        if (!$parentPage = $pageTable->find($parentPageId))
        {
          throw new dmException(sprintf('parent page with id %d for new page %s was not found', $parentPageId, $page));
        }
        //        dmDebug::show($page->getNodeParentId(), $parentPageId);
        //        dmDebug::show($page, $parentPage, $parentPage->getNode()->getChildren());
        $page->refresh();
        //        $parentPage->refresh();
        $page->getNode()->moveAsLastChildOf($parentPage);
        //        $page->refresh();
        //        $parentPage->refresh();
        //        $page->refresh();
        //        dmDebug::kill($page, $parentPage, $parentPage->getNode()->getChildren());
      }
    }

    $timer && $timer->addTime();
  }

  protected function getPageViewForModuleAndAction($module, $action)
  {
    $pageView = dmDb::table('DmPageView')
    ->createQuery('v')
    ->where('v.module = ? AND v.action = ?', array($module, 'show'))
    ->fetchRecord();

    if(!$pageView)
    {
      $pageView = dmDb::create('DmPageView', array(
        'module' => $module,
        'action' => 'show',
        'dm_layout_id' => dmDb::table('DmLayout')->findFirstOrCreate()
      ))->saveGet();
    }

    return $pageView;
  }

  protected function getParentRecordIds(dmProjectModule $module, dmProjectModule $parentModule, array $records)
  {
    /*
     * if parent is local relation for module,
     * we can prepare parent records
     */
    if ($module->hasLocal($parentModule))
    {
      $local = $module->getTable()->getRelationHolder()->getLocalByClass($parentModule->getModel())->getLocal();
      
      $_parentRecordIds = $module->getTable()->createQuery('r')
      ->select('r.id, r.'.$local)
      ->where('EXISTS (SELECT page.id FROM DmPage page WHERE page.module = ? AND page.action = ? AND page.record_id = r.'.$local.')', array($parentModule->getKey(), 'show'))
      ->fetchPDO();
      
      $parentRecordIds = array();
      foreach($_parentRecordIds as $_parentRecordId)
      {
      	$parentRecordIds[$_parentRecordId[0]] = $_parentRecordId[1];
      }
    }
    /*
     * if parent is association relation for module,
     * we can prepare parent records
     */
    elseif ($module->hasAssociation($parentModule))
    {
      $relation = $module
      ->getTable()
      ->getRelationHolder()
      ->getAssociationByClass($parentModule->getModel());

      $_parentRecordIds = $relation->getAssociationTable()
      ->createQuery('association')
      ->select('association.'.$relation['foreign'].', association.'.$relation['local'])
      //->whereIn('association.'.$relation['local'], array_keys($records))
      ->andWhere('EXISTS (SELECT page.id FROM DmPage page WHERE page.module = ? AND page.action = ? AND page.record_id = association.'.$relation['foreign'].')', array($parentModule->getKey(), 'show'))
      ->groupBy('association.'.$relation['local'])
      ->fetchPDO();

      $parentRecordIds = array();
      foreach($_parentRecordIds as $value) $parentRecordIds[$value[1]] = $value[0];
    }
    /*
     * parent records are to far to be prepared.
     * they will be evaluated later.
     */
    else
    {
      $parentRecordIds = false;
    }

    return $parentRecordIds;
  }

  
}