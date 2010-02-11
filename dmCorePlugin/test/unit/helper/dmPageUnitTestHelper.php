<?php

require_once(dirname(__FILE__).'/dmUnitTestHelper.php');

class dmPageUnitTestHelper extends dmUnitTestHelper
{
	protected
	$pageTable;

	public function initialize()
	{
    parent::initialize();

    $this->pageTable = dmDb::table('DmPage');
	}

	public function clearPages(lime_test $t)
	{
		$t->diag('destroy all pages');
		$this->pageTable->createQuery()->delete()->execute();

		  $t->is($this->pageTable->count(), 0, 'No more pages');

		$t->diag('check basic pages');
		$this->pageTable->checkBasicPages();

		  $t->is($this->pageTable->count(), 3, 'Two pages');

		$root = $this->pageTable->getTree()->fetchRoot();

		  $t->ok($root instanceof DmPage, 'Root exists');

		$error404 = $this->pageTable->findOneByModuleAndAction('main', 'error404');

		  $t->ok($error404 instanceof DmPage, 'error404 exists');

		  $t->is($error404->getNode()->getParent(), $root, 'error404 is child of root');

		  $t->is($error404->getNodeParentId(), $root->id, 'error404->getNodeParentId() returns root.id');

    $signin = $this->pageTable->findOneByModuleAndAction('main', 'signin');

      $t->ok($signin instanceof DmPage, 'signin exists');

      $t->is($signin->getNode()->getParent(), $root, 'signin is child of root');

      $t->is($signin->getNodeParentId(), $root->id, 'signin->getNodeParentId() returns root.id');
	}

	public function testI18nFetching(lime_test $t)
	{
		$nbPages = $this->pageTable->createQuery()->count();
		$nbMainPages = $this->pageTable->createQuery('p')->where('p.module = ?', 'main')->count();

		$t->diag('Fetching all pages with a culture that exists');

		$pages = $this->pageTable->createQuery('p')->withI18n()->fetchRecords();

		  $t->is(count($pages), $nbPages, $nbPages.' pages fetched');

    $t->diag('Fetching main pages with a culture that exists');

    $pages = $this->pageTable->createQuery('p')->where('p.module = ?', 'main')->withI18n()->fetchRecords();

      $t->is(count($pages), $nbMainPages, $nbMainPages.' pages fetched');

		$t->diag('Fetching all pages with a culture that does not exist');

		$pages = $this->pageTable->createQuery('p')->withI18n('__')->fetchRecords();

		  $t->is(count($pages), $nbPages, $nbPages.' pages fetched');

    $t->diag('Fetching main pages with a culture that does not exists');

    $pages = $this->pageTable->createQuery('p')->where('p.module = ?', 'main')->withI18n('__')->fetchRecords();

      $t->is(count($pages), $nbMainPages, $nbMainPages.' pages fetched');
	}

	public function testNewPage(lime_test $t)
	{
		$t->diag('Adding a page');

		$randomKey = dmString::random(4);
		$newPage = $this->createPage('main', 'test'.$randomKey, 'Test Page '.$randomKey);

		$root = $this->pageTable->getTree()->fetchRoot();

		$newPage->getNode()->insertAsLastChildOf($root);

		  $t->ok($newPage->exists(), 'newPage exists');

		  $t->is($newPage->getNode()->getParent(), $root, 'newPage is child of root');

		  $t->is($newPage->getNodeParentId(), $root->id, 'newPage->getNodeParentId() returns root.id');

		  $t->is($newPage->name, 'Test Page '.$randomKey, 'newPage->name is '.'Test Page '.$randomKey);
	}

	public function testNestedTree(lime_test $t)
	{
		$t->diag('Test nested tree');

    $table = dmDb::table('DmPage');
    $tree  = $table->getTree();
    $root  = $tree->fetchRoot();

    $b = $this->createPage('main', 'b');
    $b->getNode()->insertAsLastChildOf($root);

    $a = $this->createPage('main', 'a');
    $a->getNode()->insertAsLastChildOf($root);

    $a->refresh();
    $b->refresh();

    $t->is($a->rgt+1, $b->lft, '$a->rgt+1 == $b->lft');
    $t->ok($a->getNode()->getParent() == $root, '$root parent of $a');
    $t->ok($b->getNode()->getParent() == $root, '$root parent of $b');

    $b->getNode()->moveAsLastChildOf($a);

    $a->refresh();

    $t->is($a->lft+1, $b->lft, '$a->lft+1 == $b->lft');
    $t->is($a->rgt-1, $b->rgt, '$a->rgt-1 == $b->rgt');
    $t->ok($a->getNode()->getParent() == $root, '$root parent of $a');
    $t->ok($b->getNode()->getParent() == $a, '$a parent of $b');
    $t->is($b->getNode()->getParent()->id, $a->id, '$b->getNode()->getParent()->id == $a->id');
    $t->is($b->getNodeParentId(), $a->id, '$b->getNodeParentId() == $a->id');
	}

	protected function createPage($module, $action, $name = null, $slug = null)
	{
		$name = $name ? $name : $module.'.'.$action;
		$slug = $slug ? $slug : dmString::slugify($name);
		return dmDb::create('DmPage', array(
      'module' => $module,
		  'action' => $action,
		  'name'   => $name,
		  'slug'   => $slug
		));
	}

	public function checkTreeIntegrity(lime_test $t)
	{
    $this->checkEachPage($t); // 1 test

		$showModules = $this->getModuleManager()->getModulesWithPage();

		$errors = array();
		foreach($showModules as $moduleKey => $module)
		{
		  $errors = array_merge(
		    $errors,
		    $this->checkEachShowPageHasRecord($module, $t),
        $this->checkEachRecordHasShowPage($module, $t),
        $this->checkEachRecordChildrenHavePage($module, $t)
      );
		}

		// 1 test
    $t->is($errors, array(), "No error found in record / page correlation");
	}
	
	protected function checkEachRecordChildrenHavePage($module, $t)
	{
    $errors = array();
		/*
		 * Test only leaf modules
		 */
		if ($module->hasChildren())
		{
			return $errors;
		}
		
    if ($ancestorModule = $module->getFarthestAncestorWithPage())
    {
      $ancestorModel = $ancestorModule->getModel();
    }
    else
    {
      return $errors;
    }

      /*
       * Verify that each record wich has an ancestor has a page
       */
      $records = new myDoctrineCollection($module->getTable());
      foreach($ancestorModule->getTable()->findAll() as $ancestorRecord)
      {
      	$records = $records->merge($module->getTable()->createQuery('r')
      	->whereAncestor($ancestorRecord, $module->getModel())
      	->fetchRecords());
      }
      
     foreach($records as $record)
     {
       if (!$record->getDmPage())
     	 {
         $error = sprintf('%s %d %s has no page', $module, $record->id, $record);
         $t->diag($error); $errors[] = $error;
      	}
     }

    if(count($errors))
    {
    	$t->diag('Error !');
    	dmDebug::kill($errors);
    }
      
    return $errors;
	}

  protected function checkEachPage(lime_test $t)
  {
    $errors = 0;

    if (!$root = dmDb::table('DmPage')->getTree()->fetchRoot())
    {
      $t->diag('Tree has no root !');
      $errors++;
    }
    elseif($root->getModuleAction() != 'main/root')
    {
      $t->diag(sprintf('Root page module.action != main/root (%s)', $root->getModuleAction()));
      $errors++;
    }
    elseif($root->getNodeParentId() != null)
    {
      $t->diag(sprintf('Root->getNodeParentId() = %d', $root->getNodeParentId()));
      $errors++;
    }

    $pages = $this->pageTable->findAll();

    foreach($pages as $page)
    {
      if ($page->getNode()->isRoot()) continue;

      if (!$parent = $page->getNode()->getParent())
      {
        $t->diag(sprintf('$page->getNode()->getParent() == NULL (page : %s)', $page));
        $errors++;
      }
      elseif ($parent->id != $page->getNodeParentId())
      {
        $t->diag(sprintf('$page->getNode()->getParent()->id != $page->getNodeParentId() (%d, %d) (page : %s)', $page->getNode()->getParent()->id, $page->getNodeParentId(), $page));
        $errors++;

        if ($parent->lft >= $page->lft || $parent->rgt <= $page->rgt)
        {
	        $t->diag(sprintf('bad page / parent lft|rgt (page %d : %d, %d) (parent %d : %d, %d)', $page->id, $page->lft, $page->rgt, $parent->id, $parent->lft, $parent->rgt));
	        $errors++;
        }
      }
      if ($page->lft >= $page->rgt)
      {
        $t->diag(sprintf('$page->lft >= $page->rgt (%d, %d) (page : %s)', $page->lft, $page->rgt, $page));
        $errors++;
      }
    }

    $t->is($errors, 0, "All pages are sane ($errors errors)");
  }

  /*
   * Verify that each show page has a record
   */
	protected function checkEachShowPageHasRecord(dmProjectModule $module, lime_test $t)
	{
		$moduleKey = $module->getKey();
    $model = $module->getModel();
    if ($ancestorModule = $module->getFarthestAncestor())
    {
      $ancestorModel = $ancestorModule->getModel();
    }
    else
    {
      $ancestorModel = null;
    }
    $errors = array();

      foreach($this->pageTable->queryByModuleAndAction($moduleKey, 'show')->fetchArray() as $page)
      {
        $error = null;
        if (!$recordId = $page['record_id'])
        {
          $error = sprintf('%s page has no %s record_id', $moduleKey, $model);
        }
        elseif(!$record = dmDb::table($model)->find($recordId))
        {
          $error = sprintf('%s page has no %s record', $moduleKey, $model);
        }
        elseif($ancestorModel)
        {
          if(!$recordAncestorId = $record->getAncestorRecordId($ancestorModel))
          {
            $error = sprintf('%s page has a %s record that has no ancestor', $moduleKey, $model);
          }
        }
        else
        {
          if (!$this->pageTable->queryByModuleAndAction($moduleKey, 'list')->exists())
          {
            $error = sprintf('%s page has a no list page', $moduleKey, $model);
          }
        }
        
        if ($error)
        {
          $t->diag($error); $errors[] = $error;
        }
      }
    return $errors;
	}

	protected function checkEachRecordHasShowPage(dmProjectModule $module, lime_test $t)
	{
    $moduleKey = $module->getKey();
    $model = $module->getModel();
    if ($ancestorModule = $module->getFarthestAncestor())
    {
      $ancestorModel = $ancestorModule->getModel();
    }
    else
    {
      $ancestorModel = null;
    }
    $errors = array();

    /*
     * Verify that each record -that must have a page- has one
     */
    foreach($module->getTable()->findAll() as $record)
    {
      $page = $record->getDmPage();
      $error = null;

      if (!$ancestorModel)
      {
        if (!$page)
        {
          $error = sprintf('%s %s has no page', $model, $record);
        }
      }
      else
      {
        if ($ancestorRecord = $record->getAncestorRecord($ancestorModel))
        {
          if (!$page)
          {
            $error = sprintf('%s %s has no page', $model, $record);
          }
        }
        else
        {
          if ($page)
          {
            $error = sprintf('%s %s has a page, but no ancestor', $model, $record);
          }
        }
      }

      if ($error)
      {
        $t->diag($error); $errors[] = $error;
        continue;
      }

      if (!$page)
      {
        continue;
      }

      if ($page->record_id != $record->id)
      {
        $error = sprintf('%s page has bad record : %s', $page, $record);
        $t->diag($error); $errors[] = $error;
      }

      $parentPage = $page->getNode()->getParent();

      if ($parentModule = $module->getNearestAncestorWithPage())
      {
        if($parentPage->module != $parentModule->getKey())
        {
          $error = sprintf('parent page has bad module : %s', $parentPage->module);
          $t->diag($error); $errors[] = $error;
        }
        if($parentPage->action != 'show')
        {
          $error = sprintf('parent page has bad action : %s', $parentPage->action);
          $t->diag($error); $errors[] = $error;
        }
      }
      else
      {
        if($parentPage->module != $module->getKey())
        {
          $error = sprintf('parent page has bad module : %s', $parentPage->module);
          $t->diag($error); $errors[] = $error;
        }
        if($parentPage->action != 'list')
        {
          $error = sprintf('parent page has bad action : %s', $parentPage->action);
          $t->diag($error); $errors[] = $error;
        }
      }

    }
    return $errors;
	}

}