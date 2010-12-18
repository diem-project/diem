<?php

require_once(dirname(__FILE__).'/dmUnitTestHelper.php');

class dmMediaUnitTestHelper extends dmUnitTestHelper
{
  protected
  $folderTable,
  $mediaTable;

  public function initialize()
  {
    parent::initialize();

    $this->folderTable = dmDb::table('dmMediaFolder');
    $this->mediaTable = dmDb::table('dmMedia');
  }

  /*
   * 2 tests :
   * Verify that each db folder exist in fs
   * Verify that each fs folder exist in db
   */
  public function testFolderCorrelations(lime_test $t)
  {
    $t->diag('Verify that each db folder exist in fs');

    $errors = 0;
    foreach($this->folderTable->findAll() as $f)
    {
      if (!is_dir($f->fullPath))
      {
        $t->diag(sprintf('folder %s does not exist in fs', $f));
        $errors++;
      }
    }
    $t->is($errors, 0, 'Each db folder exist in fs');

    if ($errors > 0) die;

    $t->diag('Verify that each fs folder exist in db');

    $errors = 0;
    foreach(sfFinder::type('dir')->discard(".*")->ignore_version_control()->maxdepth(20)->in($this->folderTable->getTree()->fetchRoot()->fullPath) as $f)
    {
      if (strpos($f, '/.')) continue;
      
      $f = dmOs::normalize($f);
      
      $f = str_replace(dmOs::normalize(sfConfig::get('sf_upload_dir')).'/', '', $f);
      if (!$this->folderTable->createQuery('f')->where('f.rel_path = ?', $f)->exists())
      {
        $t->diag(sprintf('folder %s does not exist in db', $f));
        $errors++;
      }
    }
    $t->is($errors, 0, 'Each fs folder exist in db');
  }

  public function checkTreeIntegrity(lime_test $t)
  {
    $this->checkEachFolder($t); // 1 test
  }

  protected function checkEachFolder(lime_test $t)
  {
    $errors = 0;

    if (!$root = $this->folderTable->getTree()->fetchRoot())
    {
      $t->diag('Tree has no root !');
      $errors++;
    }
    elseif($root->relPath != '')
    {
      $t->diag(sprintf('Root relPath != "" (%s)', $root->relPath));
      $errors++;
    }
    elseif($root->getNodeParentId() !== null)
    {
      $t->diag(sprintf('Root->getNodeParentId() = %d', $root->getNodeParentId()));
      $errors++;
    }

    $folders = $this->folderTable->findAll();

    foreach($folders as $folder)
    {
      $folder->refresh();
      if ($folder->getNode()->isRoot()) continue;

      if (!$parent = $folder->getNode()->getParent())
      {
        $t->diag(sprintf('$folder->getNode()->getParent() == NULL (folder : %s)', $folder));
        $errors++;
      }
      elseif ($parent->id != $folder->getNodeParentId())
      {
        $t->diag(sprintf('$folder->getNode()->getParent()->id != $folder->getNodeParentId() (%d, %d) (folder : %s)', $folder->getNode()->getParent()->id, $folder->getNodeParentId(), $folder));
        $errors++;

        if ($parent->lft >= $folder->lft || $parent->rgt <= $folder->rgt)
        {
          $t->diag(sprintf('bad folder / parent lft|rgt (folder %d : %d, %d) (parent %d : %d, %d)', $folder->id, $folder->lft, $folder->rgt, $parent->id, $parent->lft, $parent->rgt));
          $errors++;
        }
      }
      if ($folder->lft >= $folder->rgt)
      {
        $t->diag(sprintf('$folder->lft >= $folder->rgt (%d, %d) (folder : %s)', $folder->lft, $folder->rgt, $folder));
        $errors++;
      }
      if (!$folder->lft || !$folder->rgt)
      {
        $t->diag(sprintf('!$folder->lft || !$folder->rgt (%d, %d) (folder : %s)', $folder->lft, $folder->rgt, $folder));
        $errors++;
      }
    }

    $t->is($errors, 0, "All folders are sane ($errors errors)");
  }

}