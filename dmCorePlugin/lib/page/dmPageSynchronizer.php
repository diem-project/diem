<?php

class dmPageSynchronizer
{
  protected
  $moduleManager,
  $nodeParentIdStmt;

  public function __construct(dmModuleManager $moduleManager)
  {
    $this->moduleManager  = $moduleManager;
  }

  public function execute(array $onlyModules = array())
  {
    if(empty($onlyModules))
    {
      $onlyModules = $this->moduleManager->getProjectModules();
    }
    elseif(is_string(dmArray::first($onlyModules)))
    {
      $onlyModules = $this->moduleManager->keysToModules($onlyModules);
    }

    $onlyModules = dmModuleManager::removeModulesChildren($onlyModules);

    $this->updateListPages();

    $this->removeShowPages($onlyModules);

    $this->updateShowPages($onlyModules);
  }

  protected function removeShowPages(array $onlyModules)
  {
    $modulesToCheck = dmDb::pdo('SELECT DISTINCT p.module FROM dm_page p WHERE p.action = ?', array('show'), dmDb::table('DmPage')->getConnection())->fetchAll(PDO::FETCH_COLUMN);

    foreach($onlyModules as $moduleKey => $module)
    {
      $this->removeModuleShowPagesRecursive($module, $modulesToCheck);
    }
  }
  
  protected function inactivePage($id)
  {
  	dmDb::pdo('UPDATE dm_page_translation t SET is_active = 0 WHERE t.id = ?', array($id));
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

    $showPages = dmDb::pdo('SELECT p.id, p.module, p.record_id FROM dm_page p WHERE p.module = ? AND p.action = ?', array($moduleKey, 'show'), dmDb::table('DmPage')->getConnection())->fetchAll(PDO::FETCH_ASSOC);

    $showPageRecordIds = array();
    foreach($showPages as $showPage)
    {
      $showPageRecordIds[] = $showPage['record_id'];
    }

    if ($module->hasListPage())
    {
      if (!empty($showPageRecordIds))
      {
      	if($module->getTable()->isSoftDelete())
      	{
      		$query = sprintf('SELECT r.id, r.deleted_at FROM %s r WHERE r.id IN (%s)',
	          $module->getTable()->getTableName(),
	          implode(',', $showPageRecordIds)
	        );
      	}else{
      		$query = sprintf('SELECT r.id FROM %s r WHERE r.id IN (%s)',
	          $module->getTable()->getTableName(),
	          implode(',', $showPageRecordIds)
	        );
      	}
        
        //$records = array_flip(dmDb::pdo($query, array(), $module->getTable()->getConnection())->fetchAll(PDO::FETCH_COLUMN));
        $_records = dmDb::pdo($query, array(), $module->getTable()->getConnection())->fetchAll(PDO::FETCH_ASSOC);
        $records = array();
        foreach($_records as $_record)
        {
          $records[$_record['id']] = $_record;
        }
      }
      else
      {
        $records = array();
      }
      
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
      
      if (!empty($showPageRecordIds))
      {
      	if($module->getTable()->isSoftDelete())
      	{
      		$query = sprintf('SELECT %s, deleted_at FROM %s r WHERE r.id IN (%s)',
	          $select,
	          $module->getTable()->getTableName(),
	          implode(',', $showPageRecordIds)
	        );
      	}else{
      		$query = sprintf('SELECT %s FROM %s r WHERE r.id IN (%s)',
	          $select,
	          $module->getTable()->getTableName(),
	          implode(',', $showPageRecordIds)
	        );
      	}
      	
        
        $_records = dmDb::pdo($query, array(), $module->getTable()->getConnection())->fetchAll(PDO::FETCH_ASSOC);
        $records = array();
        foreach($_records as $_record)
        {
          $records[$_record['id']] = $_record;
        }
      }
      else
      {
        $records = array();
      }
      
      $parentModule = $module->getNearestAncestorWithPage();
      $parentRecordIds = $this->getParentRecordIds($module, $parentModule);
    }

    foreach($showPages as $showPage)
    {
      $pageIsUseless = false;

      if(!isset($records[$showPage['record_id']]))
      {
        $pageIsUseless = true;
      }
      elseif($module->getTable()->isSoftDelete() && null !== $records[$showPage['record_id']]['deleted_at'])
      {
      	$this->inactivePage($showPage['id']);
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
          $parentRecordId = dmDb::create($module->getModel(), $record)->getAncestorRecordId($parentModule->getModel());
        }
        if (!$parentRecordId)
        {
          $pageIsUseless = true;
        }
      }

      if ($pageIsUseless)
      {
        //delete node only if it's found
        if( ($page = dmDb::table('DmPage')->find($showPage['id']) ) )
        {
          $page->getNode()->delete();
        }
      }
    }

    foreach($module->getChildren() as $child)
    {
      $this->removeModuleShowPagesRecursive($child, $modulesToCheck);
    }
  }

  protected function updateListPages()
  {
    $projectModules = $this->moduleManager->getProjectModules();
    foreach($projectModules as $key => $module)
    {
      if(!$module->hasListPage())
      {
        unset($projectModules[$key]);
      }
    }
    $projectModuleKeys = array_keys($projectModules);

    $_listPages = dmDb::pdo('SELECT p.id, p.module FROM dm_page p WHERE p.action = ?', array('list'), dmDb::table('DmPage')->getConnection())->fetchAll(PDO::FETCH_ASSOC);

    $listPages = array();
    foreach($_listPages as $_listPage)
    {
      $listPages[$_listPage['module']] = $_listPage['id'];
    }

    foreach($projectModules as $moduleKey => $module)
    {
      /*
       * Only root modules, which have no parent, need a list page
       */
      if (!isset($listPages[$moduleKey]))
      {
        if (isset($listPages[strtolower($moduleKey)]))
        {
          // fix page module
          dmDb::table('DmPage')->createQuery()
          ->update('DmPage')
          ->where('id = ?', $listPages[strtolower($moduleKey)])
          ->set('module', "'".$moduleKey."'")
          ->execute();
        }
        else
        {
          // create page
          dmDb::create('DmPage', array(
            'module'      => $moduleKey,
            'action'      => 'list',
            'name'        => $module->getPlural(),
            'title'       => $module->getPlural(),
            'slug'        => dmString::slugify($module->getPlural()),
            'description' => $module->getPlural()
          ))->getNode()->insertAsLastChildOf(dmDb::table('DmPage')->getTree()->fetchRoot());
        }
      }
    }
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

    /*
     * prepares pages to update
     */
    $_showPages = dmDb::pdo('SELECT p.id, p.module, p.record_id, p.lft, p.rgt FROM dm_page p WHERE p.module = ? AND p.action = ?', array(
      $moduleKey, 'show'
    ), dmDb::table('DmPage')->getConnection())->fetchAll(PDO::FETCH_ASSOC);
    $showPages = array();
    foreach($_showPages as $_showPage)
    {
      $showPages[$_showPage['record_id']] = $_showPage;
    }

    if ($module->hasListPage())
    {
      $parentModule = $module;

      /*
       * prepare records
       */

      // http://github.com/diem-project/diem/issues#issue/182
      if(count($module->getTable()->getOption('inheritanceMap')))
      {
        $records = $module->getTable()->createQuery('r')->select('r.id')->fetchArray();
      }
      else
      {
        $records = dmDb::pdo('SELECT r.id FROM '.$module->getTable()->getTableName().' r', array(), $module->getTable()->getConnection())->fetchAll(PDO::FETCH_ASSOC);
      }

      /*
       * prepare parent pages
       */
      $parentPageIds = dmDb::pdo('SELECT p.id FROM dm_page p WHERE p.module = ? AND p.action = ?', array($moduleKey, 'list'), dmDb::table('DmPage')->getConnection())->fetch(PDO::FETCH_NUM);
      $parentPageIds = $parentPageIds[0];

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
      $select = 'r.id';
      if ($module->hasLocal($module->getParent()))
      {
        $select .= ', r.'.$module->getTable()->getRelationHolder()->getLocalByClass($module->getParent()->getModel())->getLocal();
      }

      // http://github.com/diem-project/diem/issues#issue/182
      if(count($module->getTable()->getOption('inheritanceMap')))
      {
        $records = $module->getTable()->createQuery('r')->select($select)->fetchArray();
      }
      else
      {
        $records = dmDb::pdo('SELECT '.$select.' FROM '.$module->getTable()->getTableName().' r', array(), $module->getTable()->getConnection())->fetchAll(PDO::FETCH_ASSOC);
      }

      /*
       * prepare parent pages
       */
      $_parentPageIds = dmDb::pdo('SELECT p.id, p.record_id FROM dm_page p WHERE p.module = ? AND p.action = ?', array($parentModule->getKey(), 'show'), dmDb::table('DmPage')->getConnection())->fetchAll(PDO::FETCH_NUM);

      $parentPageIds = array();
      foreach($_parentPageIds as $value) $parentPageIds[$value[1]] = $value[0];

      $parentRecordIds = $this->getParentRecordIds($module, $parentModule);
    }

    foreach($records as $record)
    {
      if (isset($showPages[$record['id']]))
      {
        $page = $showPages[$record['id']];
      }
      else
      {
        $page = array(
          'id'        => null,
          'record_id' => $record['id'],
          'module'    => $moduleKey,
          'action'    => 'show'
        );
      }

      try
      {
        $this->updatePageFromRecord($page, $record, $module, $parentModule, $parentPageIds, $parentRecordIds);
      }
      catch(dmPageMustNotExistException $e)
      {
        if ($page['id'])
        {
          dmDb::table('DmPage')->find($page['id'])->getNode()->delete();
        }
      }
    }

    foreach($module->getChildren() as $child)
    {
      $this->updateModuleShowPagesRecursive($child);
    }
  }

  public function updatePageFromRecord(array $page, array $record, dmProjectModule $module, dmProjectModule $parentModule, $parentPageIds, $parentRecordIds)
  {
    $moduleKey    = $module->getKey();
    $recordTable  = $module->getTable();
    $pageTable    = dmDb::table('DmPage');

    //@todo make this behavior optional to not break BC ?
    if($recordTable->isNestedSet())
    {
      $recordObj = $recordTable->findOneBy($recordTable->getIdentifier(), $record['id']);
      $recordNode = $recordObj->getNode();
      if($recordNode->isRoot())
      {
        $parentPageId = $parentPageIds;
      }
      else
      {
        $parentRecord = $recordNode->getParent();
        $parentPageId = $parentRecord->getDmPage()->get('id');
      }
    }
    elseif ($parentModule->getKey() === $module->getKey()) // parent page is a list page
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
        throw new dmPageMustNotExistException(sprintf('No parent record found for %s, page %s must not exist', $module->getModel(), $page['id']));
      }
      elseif (!($parentPageId = dmArray::get($parentPageIds, $parentRecordId)))
      {
        throw new dmPageMustNotExistException(sprintf(
          '%s needs a parent page, %s.%s, but it does not exists for parent object %d',
        $module, $parentModule, 'show', $parentRecordId
        ));
      }
    }

    $modified = false;

    if (!$page['id'])
    {
      if (!$parentPage = $pageTable->find($parentPageId))
      {
        throw new dmException(sprintf('parent page with id %d for new page %s was not found', $parentPageId, $page['module'].'.show'));
      }

      dmDb::table('DmPage')->create($page)->getNode()->insertAsLastChildOf($parentPage);
    }
    else
    {
      if ($this->getNodeParentId($page) != $parentPageId)
      {
        if (!$parentPage = $pageTable->find($parentPageId))
        {
          throw new dmException(sprintf('parent page with id %d for new page %s was not found', $parentPageId, $page['module'].'.show'));
        }

        $pageRecord = dmDb::table('DmPage')->find($page['id']);
        $pageRecord->refresh(true);
        $pageRecord->getNode()->moveAsLastChildOf($parentPage);
      }
    }
  }

  protected function getNodeParentId(array $pageData)
  {
    if (null === $this->nodeParentIdStmt)
    {
      $this->nodeParentIdStmt = Doctrine_Manager::connection()->prepare('SELECT p.id
FROM dm_page p
WHERE p.lft < ? AND p.rgt > ?
ORDER BY p.rgt ASC
LIMIT 1')->getStatement();
    }

    $this->nodeParentIdStmt->execute(array($pageData['lft'], $pageData['rgt']));

    return $this->nodeParentIdStmt->fetchColumn();
  }

  protected function getParentRecordIds(dmProjectModule $module, dmProjectModule $parentModule)
  {
    /*
     * if parent is local relation for module,
     * we can prepare parent records
     */
    if ($module->hasLocal($parentModule))
    {
      $local = $module->getTable()->getRelationHolder()->getLocalByClass($parentModule->getModel())->getLocal();

      $query = sprintf('SELECT r.id, r.%s FROM %s r WHERE EXISTS (SELECT page.id FROM dm_page page WHERE page.module = ? AND page.action = ? AND page.record_id = r.%s)',
        $local,
        $module->getTable()->getTableName(),
        $local
      );
      $_parentRecordIds = dmDb::pdo($query, array($parentModule->getKey(), 'show'), $module->getTable()->getConnection())->fetchAll(PDO::FETCH_NUM);

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
      $association = $module
      ->getTable()
      ->getRelationHolder()
      ->getAssociationByClass($parentModule->getModel());

      $query = sprintf('SELECT association.%s, association.%s FROM %s association WHERE EXISTS (SELECT page.id FROM dm_page page WHERE page.module = ? AND page.action = ? AND page.record_id = association.%s) GROUP BY association.%s',
        $association->getForeign(),
        $association->getLocal(),
        $association->getAssociationTable()->getTableName(),
        $association->getForeign(),
        $association->getLocal()
      );

      $_parentRecordIds = dmDb::pdo($query, array($parentModule->getKey(), 'show'), $module->getTable()->getConnection())->fetchAll(PDO::FETCH_NUM);

      $parentRecordIds = array();
      foreach($_parentRecordIds as $value)
      {
        $parentRecordIds[$value[1]] = $value[0];
      }
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
