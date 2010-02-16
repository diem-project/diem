<?php

class dmSyncPagesTask extends dmContextTask
{

  /**
   * @see sfTask
   */
  protected function configure()
  {
    parent::configure();
    
    $this->namespace = 'dm';
    $this->name = 'sync-pages';
    $this->briefDescription = 'Synchronize pages according to modules and records.';

    $this->detailedDescription = $this->briefDescription;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $this->withDatabase();
    
    $treeWatcher = $this->get('page_tree_watcher');
    
    $treeWatcher->setOption('use_thread', false);
    
    $this->logSection('diem', 'Synchronize pages... this may take some time');
    
    $treeWatcher->synchronizePages();
    $this->log(sprintf('%d pages', dmDb::table('DmPage')->count()));
    
    $this->logSection('diem', 'Synchronize SEO... this may take some time');
    
    $treeWatcher->synchronizeSeo();
    $this->log(sprintf('%d page translations', dmDb::table('DmPageTranslation')->count()));
    
    $this->logSection('diem', 'Done.');
  }
}